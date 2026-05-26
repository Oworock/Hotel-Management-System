@extends('layouts.app')

@section('title', 'Notification Manager')

@section('content')
<div class="animate-fade-in" style="display:grid;gap:1.5rem;">
    <div style="display:flex;justify-content:space-between;gap:1rem;align-items:flex-start;flex-wrap:wrap;">
        <div>
            <h1 style="font-size:1.6rem;font-weight:800;color:var(--text-primary);margin:0;">Notification Manager</h1>
            <p style="color:var(--text-secondary);margin:.35rem 0 0;">Manage booking, check-in, checkout, staff, SMS, email, templates, recipients, and delivery logs.</p>
        </div>
        <form action="{{ route('notification_manager.test') }}" method="POST" class="glass-panel" style="padding:1rem;display:flex;gap:.75rem;align-items:end;flex-wrap:wrap;">
            @csrf
            <div class="form-group" style="margin:0;min-width:220px;">
                <label class="form-label">Test Event</label>
                <select name="event_key" class="form-control">
                    @foreach($events as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin:0;min-width:160px;">
                <label class="form-label">Guest/Staff Name</label>
                <input type="text" name="guest_name" class="form-control" value="Demo Guest">
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Send Test</button>
        </form>
    </div>

    <div class="glass-panel" style="padding:1.5rem;">
        <h2 style="font-size:1.1rem;font-weight:750;color:var(--text-primary);margin:0 0 1rem;">Global Notification Settings</h2>
        <form action="{{ route('notification_manager.settings.update') }}" method="POST" style="display:grid;gap:1rem;">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Default Recipient Roles</label>
                    <div style="display:flex;gap:1rem;flex-wrap:wrap;color:var(--text-primary);">
                        @foreach(['super_admin' => 'Super Admin', 'admin' => 'Admin', 'receptionist' => 'Receptionist', 'staff' => 'Staff'] as $role => $label)
                            <label style="display:flex;align-items:center;gap:.45rem;">
                                <input type="checkbox" name="default_recipient_roles[]" value="{{ $role }}" {{ in_array($role, $settings['default_recipient_roles'], true) ? 'checked' : '' }}>
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Notification Signature</label>
                    <input type="text" name="notification_signature" class="form-control" value="{{ $settings['notification_signature'] }}">
                </div>
            </div>
            <div style="display:flex;justify-content:flex-end;">
                <button class="btn btn-outline" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save Settings</button>
            </div>
        </form>
    </div>

    <div class="glass-panel" style="padding:1.5rem;">
        <h2 style="font-size:1.1rem;font-weight:750;color:var(--text-primary);margin:0 0 1rem;">Notification Templates</h2>
        <div style="display:grid;gap:.65rem;max-height:620px;overflow:auto;padding-right:.25rem;">
            @foreach($templates as $template)
                <details class="glass-panel" style="padding:.8rem 1rem;border-color:var(--border-color);">
                    <summary style="cursor:pointer;display:flex;justify-content:space-between;gap:1rem;align-items:center;color:var(--text-primary);font-weight:700;">
                        <span>{{ $template->name }}</span>
                        <span style="display:flex;gap:.45rem;align-items:center;flex-wrap:wrap;">
                            @if($template->send_email)<span class="badge badge-info">Email</span>@endif
                            @if($template->send_sms)<span class="badge badge-warning">SMS</span>@endif
                            <span class="badge {{ $template->is_active ? 'badge-success' : 'badge-outline' }}">{{ $template->is_active ? 'Active' : 'Inactive' }}</span>
                        </span>
                    </summary>
                    <form action="{{ route('notification_manager.templates.update', $template) }}" method="POST" style="display:grid;gap:.85rem;margin-top:1rem;">
                        @csrf
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Template Name</label>
                                <input name="name" class="form-control" value="{{ $template->name }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email Subject</label>
                                <input name="email_subject" class="form-control" value="{{ $template->email_subject }}">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Email Body</label>
                                <textarea name="email_body" class="form-control" rows="4">{{ $template->email_body }}</textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">SMS Body</label>
                                <textarea name="sms_body" class="form-control" rows="4" maxlength="640">{{ $template->sms_body }}</textarea>
                            </div>
                        </div>
                        <div style="display:flex;gap:1.25rem;flex-wrap:wrap;color:var(--text-primary);">
                            <label><input type="checkbox" name="send_email" value="1" {{ $template->send_email ? 'checked' : '' }}> Email</label>
                            <label><input type="checkbox" name="send_sms" value="1" {{ $template->send_sms ? 'checked' : '' }}> SMS</label>
                            <label><input type="checkbox" name="is_active" value="1" {{ $template->is_active ? 'checked' : '' }}> Active</label>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Recipient Roles For This Event</label>
                            <div style="display:flex;gap:1rem;flex-wrap:wrap;color:var(--text-primary);">
                                @foreach(['super_admin' => 'Super Admin', 'admin' => 'Admin', 'receptionist' => 'Receptionist', 'staff' => 'Staff'] as $role => $label)
                                    <label style="display:flex;align-items:center;gap:.45rem;">
                                        <input type="checkbox" name="recipient_roles[]" value="{{ $role }}" {{ in_array($role, $template->recipient_roles ?: [], true) ? 'checked' : '' }}>
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div style="display:flex;justify-content:flex-end;">
                            <button class="btn btn-primary" type="submit"><i class="fa-solid fa-check"></i> Save Template</button>
                        </div>
                    </form>
                </details>
            @endforeach
        </div>
    </div>

    <div class="glass-panel" style="padding:1.5rem;">
        <h2 style="font-size:1.1rem;font-weight:750;color:var(--text-primary);margin:0 0 1rem;">Manual Recipients</h2>
        <form action="{{ route('notification_manager.recipients.store') }}" method="POST" style="display:grid;gap:1rem;margin-bottom:1.25rem;">
            @csrf
            <div class="form-row">
                <div class="form-group"><label class="form-label">Name</label><input name="name" class="form-control" required></div>
                <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" class="form-control"></div>
                <div class="form-group"><label class="form-label">Phone</label><input name="phone" class="form-control"></div>
            </div>
            <div style="display:flex;gap:1rem;align-items:center;flex-wrap:wrap;color:var(--text-primary);">
                <label><input type="checkbox" name="wants_email" value="1" checked> Email</label>
                <label><input type="checkbox" name="wants_sms" value="1" checked> SMS</label>
                <label><input type="checkbox" name="is_active" value="1" checked> Active</label>
                <button class="btn btn-outline" type="submit"><i class="fa-solid fa-plus"></i> Add Recipient</button>
            </div>
        </form>

        <div class="table-container">
            <table class="table">
                <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Channels</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse($recipients as $recipient)
                        <tr>
                            <td>{{ $recipient->name }}</td>
                            <td>{{ $recipient->email ?: '-' }}</td>
                            <td>{{ $recipient->phone ?: '-' }}</td>
                            <td>{{ $recipient->wants_email ? 'Email' : '' }} {{ $recipient->wants_sms ? 'SMS' : '' }}</td>
                            <td>{{ $recipient->is_active ? 'Active' : 'Inactive' }}</td>
                            <td>
                                <form action="{{ route('notification_manager.recipients.delete', $recipient) }}" method="POST" onsubmit="return confirm('Remove this notification recipient?')">
                                    @csrf
                                    <button class="btn btn-danger" type="submit" style="padding:.35rem .65rem;"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align:center;color:var(--text-secondary);padding:2rem;">No manual recipients yet. Role-based recipients will still be used.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="glass-panel" style="padding:1.5rem;">
        <h2 style="font-size:1.1rem;font-weight:750;color:var(--text-primary);margin:0 0 1rem;">Recent Delivery Logs</h2>
        <div class="table-container">
            <table class="table">
                <thead><tr><th>Date</th><th>Event</th><th>Channel</th><th>Recipient</th><th>Status</th><th>Message</th></tr></thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->created_at?->format('Y-m-d H:i') }}</td>
                            <td>{{ $events[$log->event_key] ?? $log->event_key }}</td>
                            <td>{{ strtoupper($log->channel) }}</td>
                            <td>{{ $log->recipient_name ?: $log->recipient_email ?: $log->recipient_phone }}</td>
                            <td><span class="badge {{ $log->status === 'sent' ? 'badge-success' : 'badge-danger' }}">{{ $log->status }}</span></td>
                            <td style="max-width:360px;white-space:normal;">{{ $log->message }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align:center;color:var(--text-secondary);padding:2rem;">No notification logs yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
