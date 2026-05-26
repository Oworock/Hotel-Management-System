<?php

namespace Plugins\NotificationManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Plugins\NotificationManager\Models\NotificationInbox;
use Plugins\NotificationManager\Models\NotificationLog;
use Plugins\NotificationManager\Models\NotificationPreference;
use Plugins\NotificationManager\Models\NotificationRecipient;
use Plugins\NotificationManager\Models\NotificationTemplate;
use Plugins\NotificationManager\Services\NotificationManagerService;

class NotificationManagerController extends Controller
{
    public function index(NotificationManagerService $service)
    {
        abort_unless(Schema::hasTable('notification_manager_templates'), 503, 'Notification Manager tables are not migrated yet.');

        $service->ensureDefaultConfiguration();

        return view('notification-manager::notifications.index', [
            'templates' => NotificationTemplate::orderBy('event_key')->get(),
            'recipients' => NotificationRecipient::latest()->get(),
            'logs' => NotificationLog::latest()->limit(50)->get(),
            'events' => NotificationManagerService::EVENTS,
            'settings' => [
                'default_recipient_roles' => json_decode(NotificationPreference::getValue('default_recipient_roles', '[]') ?: '[]', true) ?: [],
                'notification_signature' => NotificationPreference::getValue('notification_signature', 'MainStay HMS Notifications'),
            ],
        ]);
    }

    public function updateTemplate(Request $request, NotificationTemplate $template)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email_subject' => 'nullable|string|max:255',
            'email_body' => 'nullable|string',
            'sms_body' => 'nullable|string|max:640',
            'recipient_roles' => 'nullable|array',
            'recipient_roles.*' => 'string|max:80',
            'send_email' => 'nullable|boolean',
            'send_sms' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $data['send_email'] = $request->boolean('send_email');
        $data['send_sms'] = $request->boolean('send_sms');
        $data['is_active'] = $request->boolean('is_active');
        $data['recipient_roles'] = array_values($data['recipient_roles'] ?? []);

        $template->update($data);

        return back()->with('success', 'Notification template updated successfully.');
    }

    public function storeRecipient(Request $request)
    {
        $data = $this->validateRecipient($request);
        NotificationRecipient::create($data);

        return back()->with('success', 'Notification recipient added successfully.');
    }

    public function updateRecipient(Request $request, NotificationRecipient $recipient)
    {
        $recipient->update($this->validateRecipient($request));

        return back()->with('success', 'Notification recipient updated successfully.');
    }

    public function deleteRecipient(NotificationRecipient $recipient)
    {
        $recipient->delete();

        return back()->with('success', 'Notification recipient removed successfully.');
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'default_recipient_roles' => 'nullable|array',
            'default_recipient_roles.*' => 'string|max:80',
            'notification_signature' => 'nullable|string|max:255',
        ]);

        NotificationPreference::setValue('default_recipient_roles', json_encode(array_values($data['default_recipient_roles'] ?? [])));
        NotificationPreference::setValue('notification_signature', $data['notification_signature'] ?? 'MainStay HMS Notifications');

        return back()->with('success', 'Notification settings saved successfully.');
    }

    public function sendTest(Request $request, NotificationManagerService $service)
    {
        $data = $request->validate([
            'event_key' => ['required', Rule::in(array_keys(NotificationManagerService::EVENTS))],
            'guest_name' => 'nullable|string|max:255',
            'room_number' => 'nullable|string|max:80',
        ]);

        $result = $service->notify($data['event_key'], [
            'guest_name' => $data['guest_name'] ?? 'Demo Guest',
            'room_number' => $data['room_number'] ?? '101',
            'booking_id' => 'TEST',
            'check_in_date' => now()->format('Y-m-d'),
            'check_out_date' => now()->addDays(2)->format('Y-m-d'),
            'booking_status' => 'test',
            'payment_status' => 'test',
            'booking_total' => '0.00',
            'staff_name' => auth()->user()?->name ?: 'Demo Staff',
            'staff_role' => 'Receptionist',
            'staff_status' => 'active',
            'sent_at' => now()->format('Y-m-d H:i'),
        ]);

        return back()->with('success', $result['message'] . " Dashboard: {$result['dashboard']}. Sent: {$result['sent']}. Failed: {$result['failed']}.");
    }

    public function inbox(Request $request)
    {
        abort_unless(Schema::hasTable('notification_manager_inbox'), 503, 'Notification inbox table is not migrated yet.');

        $user = $request->user();
        $notifications = NotificationInbox::where('user_id', $user->id)
            ->whereNull('read_at')
            ->latest()
            ->limit(12)
            ->get()
            ->map(fn (NotificationInbox $notification) => [
                'id' => $notification->id,
                'event_key' => $notification->event_key,
                'title' => $notification->title,
                'message' => $notification->message,
                'open_url' => route('notification_manager.inbox.open', $notification),
                'read' => (bool) $notification->read_at,
                'created_at' => $notification->created_at?->diffForHumans(),
                'timestamp' => $notification->created_at?->timestamp,
            ]);

        $unreadCount = NotificationInbox::where('user_id', $user->id)->whereNull('read_at')->count();

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $notifications,
            'latest_id' => $notifications->max('id') ?: 0,
        ]);
    }

    public function markInboxRead(Request $request)
    {
        abort_unless(Schema::hasTable('notification_manager_inbox'), 503, 'Notification inbox table is not migrated yet.');

        NotificationInbox::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function openInboxItem(Request $request, NotificationInbox $notification)
    {
        abort_unless($notification->user_id === $request->user()->id, 403);

        if (!$notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        return redirect()->away($notification->action_url ?: route('notification_manager.index'));
    }

    private function validateRecipient(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|required_without:phone',
            'phone' => 'nullable|string|max:40|required_without:email',
            'role' => 'nullable|string|max:80',
            'event_keys' => 'nullable|array',
            'event_keys.*' => ['string', Rule::in(array_keys(NotificationManagerService::EVENTS))],
            'wants_email' => 'nullable|boolean',
            'wants_sms' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $data['event_keys'] = array_values($data['event_keys'] ?? []);
        $data['wants_email'] = $request->boolean('wants_email');
        $data['wants_sms'] = $request->boolean('wants_sms');
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
