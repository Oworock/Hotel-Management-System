<?php

namespace Plugins\NotificationManager\Services;

use App\Models\Booking;
use App\Models\Setting;
use App\Models\StaffShift;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Plugins\NotificationManager\Models\NotificationInbox;
use Plugins\NotificationManager\Models\NotificationLog;
use Plugins\NotificationManager\Models\NotificationPreference;
use Plugins\NotificationManager\Models\NotificationRecipient;
use Plugins\NotificationManager\Models\NotificationTemplate;

class NotificationManagerService
{
    public const EVENTS = [
        'booking.created' => 'Guest booking created',
        'booking.confirmed' => 'Guest booking confirmed',
        'booking.checked_in' => 'Guest checked in',
        'booking.checked_out' => 'Guest checked out',
        'booking.cancelled' => 'Guest booking cancelled',
        'staff.created' => 'Staff account created',
        'staff.status_changed' => 'Staff status changed',
        'staff.shift_started' => 'Staff shift started',
        'staff.shift_ended' => 'Staff shift ended',
        'system.test' => 'Manual test notification',
    ];

    public function ensureDefaultConfiguration(): void
    {
        if (!Schema::hasTable('notification_manager_templates')) {
            return;
        }

        foreach ($this->defaultTemplates() as $eventKey => $template) {
            NotificationTemplate::firstOrCreate(
                ['event_key' => $eventKey],
                $template + ['event_key' => $eventKey]
            );
        }

        if (Schema::hasTable('notification_manager_preferences')) {
            NotificationPreference::firstOrCreate(
                ['key' => 'default_recipient_roles'],
                ['value' => json_encode(['super_admin', 'admin', 'receptionist'])]
            );
            NotificationPreference::firstOrCreate(
                ['key' => 'notification_signature'],
                ['value' => 'MainStay HMS Notifications']
            );
        }
    }

    public function notify(string $eventKey, array $payload = []): array
    {
        if (!Schema::hasTable('notification_manager_templates') || !Schema::hasTable('notification_manager_logs')) {
            return ['sent' => 0, 'failed' => 0, 'message' => 'Notification Manager tables are not migrated yet.'];
        }

        $this->ensureDefaultConfiguration();

        $template = NotificationTemplate::where('event_key', $eventKey)->first();
        if (!$template || !$template->is_active) {
            return ['sent' => 0, 'failed' => 0, 'message' => "No active template for {$eventKey}."];
        }

        $payload = array_merge($this->basePayload(), $payload, ['event_key' => $eventKey]);
        $recipients = $this->resolveRecipients($template, $eventKey, $payload);
        $sent = 0;
        $failed = 0;
        $dashboard = $this->createDashboardNotifications($template, $recipients, $eventKey, $payload);

        foreach ($recipients as $recipient) {
            if ($template->send_email && ($recipient['wants_email'] ?? true) && !empty($recipient['email'])) {
                $result = $this->sendEmail(
                    $recipient,
                    $eventKey,
                    $this->render($template->email_subject ?: $template->name, $payload),
                    $this->render($template->email_body ?: '', $payload),
                    $payload
                );
                $result ? $sent++ : $failed++;
            }

            if ($template->send_sms && ($recipient['wants_sms'] ?? true) && !empty($recipient['phone'])) {
                $result = $this->sendSms(
                    $recipient,
                    $eventKey,
                    $this->render($template->sms_body ?: '', $payload),
                    $payload
                );
                $result ? $sent++ : $failed++;
            }
        }

        return [
            'sent' => $sent,
            'failed' => $failed,
            'dashboard' => $dashboard,
            'message' => "Notification dispatch completed for {$eventKey}.",
        ];
    }

    public function payloadForBooking(Booking $booking): array
    {
        $booking->loadMissing(['customer', 'room']);
        $customer = $booking->customer;
        $room = $booking->room;

        return [
            'booking_id' => $booking->id,
            'customer_id' => $booking->customer_id,
            'guest_name' => $customer?->name ?: 'Guest',
            'guest_email' => $customer?->email,
            'guest_phone' => $customer?->phone,
            'room_number' => $room?->room_number ?: 'Unassigned',
            'check_in_date' => optional($booking->check_in_date)->format('Y-m-d'),
            'check_out_date' => optional($booking->check_out_date)->format('Y-m-d'),
            'booking_status' => $booking->status,
            'payment_status' => $booking->payment_status,
            'booking_total' => number_format((float) $booking->total_price, 2),
        ];
    }

    public function payloadForStaff(User $user, array $extra = []): array
    {
        return array_merge([
            'staff_id' => $user->id,
            'staff_name' => $user->name,
            'staff_email' => $user->email,
            'staff_phone' => $user->phone,
            'staff_role' => Str::headline((string) $user->role),
            'staff_status' => $user->status,
        ], $extra);
    }

    public function payloadForShift(StaffShift $shift): array
    {
        $shift->loadMissing('user');
        $user = $shift->user;

        return $this->payloadForStaff($user ?: new User(['name' => 'Staff Member']), [
            'shift_id' => $shift->id,
            'clock_in_at' => optional($shift->clock_in_at)->format('Y-m-d H:i'),
            'clock_out_at' => optional($shift->clock_out_at)->format('Y-m-d H:i'),
            'duration_minutes' => $shift->duration_minutes,
        ]);
    }

    public function defaultTemplates(): array
    {
        return [
            'booking.created' => [
                'name' => 'New Guest Booking',
                'email_subject' => 'New booking received: #{{ booking_id }}',
                'email_body' => "A new booking has been created.\n\nGuest: {{ guest_name }}\nRoom: {{ room_number }}\nArrival: {{ check_in_date }}\nDeparture: {{ check_out_date }}\nTotal: {{ currency }}{{ booking_total }}\nStatus: {{ booking_status }}",
                'sms_body' => 'New booking #{{ booking_id }} for {{ guest_name }}. Room {{ room_number }}, {{ check_in_date }} to {{ check_out_date }}.',
                'send_email' => true,
                'send_sms' => true,
                'recipient_roles' => ['super_admin', 'admin', 'receptionist'],
            ],
            'booking.confirmed' => [
                'name' => 'Booking Confirmed',
                'email_subject' => 'Booking #{{ booking_id }} confirmed',
                'email_body' => "Booking #{{ booking_id }} has been confirmed for {{ guest_name }}.\nRoom: {{ room_number }}\nArrival: {{ check_in_date }}",
                'sms_body' => 'Booking #{{ booking_id }} confirmed for {{ guest_name }}. Room {{ room_number }}.',
                'send_email' => true,
                'send_sms' => true,
                'recipient_roles' => ['super_admin', 'admin', 'receptionist'],
            ],
            'booking.checked_in' => [
                'name' => 'Guest Checked In',
                'email_subject' => '{{ guest_name }} checked in',
                'email_body' => "{{ guest_name }} has checked in.\nBooking: #{{ booking_id }}\nRoom: {{ room_number }}\nChecked in for: {{ check_in_date }} - {{ check_out_date }}",
                'sms_body' => '{{ guest_name }} checked in to room {{ room_number }}. Booking #{{ booking_id }}.',
                'send_email' => true,
                'send_sms' => true,
                'recipient_roles' => ['super_admin', 'admin', 'receptionist'],
            ],
            'booking.checked_out' => [
                'name' => 'Guest Checked Out',
                'email_subject' => '{{ guest_name }} checked out',
                'email_body' => "{{ guest_name }} has checked out.\nBooking: #{{ booking_id }}\nRoom: {{ room_number }}\nPayment: {{ payment_status }}",
                'sms_body' => '{{ guest_name }} checked out from room {{ room_number }}. Payment: {{ payment_status }}.',
                'send_email' => true,
                'send_sms' => true,
                'recipient_roles' => ['super_admin', 'admin', 'receptionist'],
            ],
            'booking.cancelled' => [
                'name' => 'Booking Cancelled',
                'email_subject' => 'Booking #{{ booking_id }} cancelled',
                'email_body' => "Booking #{{ booking_id }} for {{ guest_name }} has been cancelled.\nRoom: {{ room_number }}\nArrival: {{ check_in_date }}",
                'sms_body' => 'Booking #{{ booking_id }} for {{ guest_name }} has been cancelled.',
                'send_email' => true,
                'send_sms' => true,
                'recipient_roles' => ['super_admin', 'admin', 'receptionist'],
            ],
            'staff.created' => [
                'name' => 'Staff Account Created',
                'email_subject' => 'New staff account: {{ staff_name }}',
                'email_body' => "A staff account has been created.\nName: {{ staff_name }}\nRole: {{ staff_role }}\nStatus: {{ staff_status }}",
                'sms_body' => 'New staff account created: {{ staff_name }} ({{ staff_role }}).',
                'send_email' => true,
                'send_sms' => false,
                'recipient_roles' => ['super_admin', 'admin'],
            ],
            'staff.status_changed' => [
                'name' => 'Staff Status Changed',
                'email_subject' => 'Staff status updated: {{ staff_name }}',
                'email_body' => "{{ staff_name }} status changed from {{ old_status }} to {{ staff_status }}.\nRole: {{ staff_role }}",
                'sms_body' => '{{ staff_name }} status changed to {{ staff_status }}.',
                'send_email' => true,
                'send_sms' => false,
                'recipient_roles' => ['super_admin', 'admin'],
            ],
            'staff.shift_started' => [
                'name' => 'Staff Shift Started',
                'email_subject' => '{{ staff_name }} started shift',
                'email_body' => "{{ staff_name }} started a shift at {{ clock_in_at }}.\nRole: {{ staff_role }}",
                'sms_body' => '{{ staff_name }} started shift at {{ clock_in_at }}.',
                'send_email' => true,
                'send_sms' => false,
                'recipient_roles' => ['super_admin', 'admin'],
            ],
            'staff.shift_ended' => [
                'name' => 'Staff Shift Ended',
                'email_subject' => '{{ staff_name }} ended shift',
                'email_body' => "{{ staff_name }} ended a shift at {{ clock_out_at }}.\nDuration: {{ duration_minutes }} minutes.",
                'sms_body' => '{{ staff_name }} ended shift at {{ clock_out_at }}.',
                'send_email' => true,
                'send_sms' => false,
                'recipient_roles' => ['super_admin', 'admin'],
            ],
            'system.test' => [
                'name' => 'Manual Test Notification',
                'email_subject' => 'Notification Manager test',
                'email_body' => "This is a test notification from {{ app_name }}.\nSent at: {{ sent_at }}",
                'sms_body' => 'Test notification from {{ app_name }} at {{ sent_at }}.',
                'send_email' => true,
                'send_sms' => true,
                'recipient_roles' => ['super_admin', 'admin'],
            ],
        ];
    }

    private function resolveRecipients(NotificationTemplate $template, string $eventKey, array $payload = []): Collection
    {
        $roles = $template->recipient_roles ?: $this->defaultRecipientRoles();
        $users = collect();

        if (!empty($roles) && Schema::hasTable('users')) {
            $users = User::query()
                ->whereIn('role', $roles)
                ->where(function ($query) {
                    $query->whereNull('status')->orWhere('status', 'active');
                })
                ->get()
                ->map(fn (User $user) => [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'wants_email' => true,
                    'wants_sms' => true,
                ]);
        }

        $customer = $this->resolveCustomerRecipient($eventKey, $payload ?? []);
        if ($customer) {
            $users = $users->push($customer);
        }

        $manual = collect();
        if (Schema::hasTable('notification_manager_recipients')) {
            $manual = NotificationRecipient::where('is_active', true)
                ->get()
                ->filter(function (NotificationRecipient $recipient) use ($eventKey) {
                    $events = $recipient->event_keys ?: [];
                    return empty($events) || in_array($eventKey, $events, true);
                })
                ->map(fn (NotificationRecipient $recipient) => [
                    'user_id' => null,
                    'name' => $recipient->name,
                    'email' => $recipient->email,
                    'phone' => $recipient->phone,
                    'wants_email' => $recipient->wants_email,
                    'wants_sms' => $recipient->wants_sms,
                ]);
        }

        return $users
            ->merge($manual)
            ->unique(fn (array $recipient) => $recipient['user_id'] ? 'user:' . $recipient['user_id'] : strtolower(($recipient['email'] ?: '') . '|' . ($recipient['phone'] ?: '')))
            ->values();
    }

    private function createDashboardNotifications(NotificationTemplate $template, Collection $recipients, string $eventKey, array $payload): int
    {
        if (!Schema::hasTable('notification_manager_inbox')) {
            return 0;
        }

        $title = $this->render($template->email_subject ?: $template->name, $payload);
        $message = trim($this->render($template->sms_body ?: $template->email_body ?: $template->name, $payload));
        $created = 0;

        foreach ($recipients as $recipient) {
            if (empty($recipient['user_id'])) {
                continue;
            }

            NotificationInbox::create([
                'user_id' => $recipient['user_id'],
                'event_key' => $eventKey,
                'title' => Str::limit($title, 180, ''),
                'message' => Str::limit($message, 500, ''),
                'action_url' => $this->actionUrlForRecipient($recipient, $eventKey, $payload),
                'payload' => $payload,
            ]);
            $created++;
        }

        return $created;
    }

    private function resolveCustomerRecipient(string $eventKey, array $payload): ?array
    {
        if (!str_starts_with($eventKey, 'booking.') || !Schema::hasTable('users')) {
            return null;
        }

        $bookingId = $payload['booking_id'] ?? request()->route('booking')?->id ?? null;
        if (!$bookingId) {
            return null;
        }

        $booking = Booking::with('customer')->find($bookingId);
        $customer = $booking?->customer;
        if (!$customer) {
            return null;
        }

        return [
            'user_id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'wants_email' => true,
            'wants_sms' => false,
        ];
    }

    private function actionUrlForRecipient(array $recipient, string $eventKey, array $payload): string
    {
        $role = null;
        if (!empty($recipient['user_id']) && Schema::hasTable('users')) {
            $role = User::whereKey($recipient['user_id'])->value('role');
        }

        if (str_starts_with($eventKey, 'booking.')) {
            $bookingId = $payload['booking_id'] ?? null;
            $guestSearch = $payload['guest_email'] ?? $payload['guest_name'] ?? null;

            if ($role === 'customer' && $bookingId && Route::has('customer.bookings.document')) {
                return route('customer.bookings.document', $bookingId);
            }

            if ($role === 'receptionist' && Route::has('receptionist.bookings')) {
                return route('receptionist.bookings', array_filter([
                    'search' => $guestSearch,
                    'status' => $payload['booking_status'] ?? null,
                ]));
            }

            if ($role === 'staff' && Route::has('staff.bookings')) {
                return route('staff.bookings');
            }

            if (Route::has('admin.bookings')) {
                return route('admin.bookings', array_filter([
                    'search' => $guestSearch,
                    'status' => $payload['booking_status'] ?? null,
                ]));
            }
        }

        if (str_starts_with($eventKey, 'staff.')) {
            if (Route::has('admin.hr.employees.index')) {
                return route('admin.hr.employees.index');
            }
            if (Route::has('admin.users')) {
                return route('admin.users');
            }
        }

        return Route::has('notification_manager.index') ? route('notification_manager.index') : url('/');
    }

    private function defaultRecipientRoles(): array
    {
        $roles = json_decode(NotificationPreference::getValue('default_recipient_roles', '[]') ?: '[]', true);

        return is_array($roles) && !empty($roles) ? $roles : ['super_admin', 'admin', 'receptionist'];
    }

    private function sendEmail(array $recipient, string $eventKey, string $subject, string $body, array $payload): bool
    {
        try {
            Mail::raw($body, function ($message) use ($recipient, $subject) {
                $message->to($recipient['email'], $recipient['name'] ?? null)->subject($subject);
            });

            $this->log($eventKey, 'email', $recipient, 'sent', $subject, $payload);
            return true;
        } catch (\Throwable $e) {
            $this->log($eventKey, 'email', $recipient, 'failed', $e->getMessage(), $payload);
            report($e);
            return false;
        }
    }

    private function sendSms(array $recipient, string $eventKey, string $body, array $payload): bool
    {
        try {
            $result = SmsService::send($recipient['phone'], $body);
            $status = $result['success'] ? 'sent' : 'failed';
            $this->log($eventKey, 'sms', $recipient, $status, $result['message'] ?? $body, $payload);

            return (bool) $result['success'];
        } catch (\Throwable $e) {
            $this->log($eventKey, 'sms', $recipient, 'failed', $e->getMessage(), $payload);
            report($e);
            return false;
        }
    }

    private function log(string $eventKey, string $channel, array $recipient, string $status, string $message, array $payload): void
    {
        NotificationLog::create([
            'event_key' => $eventKey,
            'channel' => $channel,
            'recipient_name' => $recipient['name'] ?? null,
            'recipient_email' => $recipient['email'] ?? null,
            'recipient_phone' => $recipient['phone'] ?? null,
            'status' => $status,
            'message' => $message,
            'payload' => $payload,
            'sent_at' => $status === 'sent' ? now() : null,
        ]);
    }

    private function render(string $content, array $payload): string
    {
        foreach ($payload as $key => $value) {
            $value = is_scalar($value) || $value === null ? (string) $value : json_encode($value);
            $content = str_replace(['{{ ' . $key . ' }}', '{{' . $key . '}}', '{' . $key . '}'], $value, $content);
        }

        return $content;
    }

    private function basePayload(): array
    {
        return [
            'app_name' => config('app.name', 'MainStay HMS'),
            'hotel_name' => Setting::getValue('hotel_name', config('app.name', 'MainStay HMS')),
            'currency' => Setting::getValue('currency', '$'),
            'sent_at' => now()->format('Y-m-d H:i'),
            'signature' => NotificationPreference::getValue('notification_signature', 'MainStay HMS Notifications'),
        ];
    }
}
