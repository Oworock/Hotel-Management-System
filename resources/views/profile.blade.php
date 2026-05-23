@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="glass-panel animate-fade-in" style="max-width: 600px; margin: 0 auto; padding: 2.5rem;">
    <div style="text-align: center; margin-bottom: 2rem;">
        <div style="width: 80px; height: 80px; border-radius: var(--radius-full); background: linear-gradient(135deg, var(--primary), var(--secondary)); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 2.25rem; box-shadow: var(--shadow-md); margin-bottom: 1rem;">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary);">Account Settings</h2>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">
            Manage your personal login credentials and display details.
        </p>
    </div>

    <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="form-group">
            <label for="profile-name" class="form-label">Full Name</label>
            <input type="text" name="name" id="profile-name" class="form-control" value="{{ old('name', $user->name) }}" required placeholder="Your full name">
        </div>

        <div class="form-group" style="margin-top: 1.25rem;">
            <label for="profile-email" class="form-label">Email Address</label>
            <input type="email" name="email" id="profile-email" class="form-control" value="{{ old('email', $user->email) }}" required placeholder="name@hotel.com">
        </div>

        <div style="margin: 2.5rem 0 1.5rem 0; border-top: 1px dashed var(--border-color); padding-top: 1.5rem;">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--text-primary);">Change Password</h3>
            <p style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 1.25rem;">Leave password fields blank if you do not wish to change your current password.</p>
        </div>

        <div class="form-group">
            <label for="profile-password" class="form-label">New Password</label>
            <input type="password" name="password" id="profile-password" class="form-control" placeholder="Minimum 8 characters">
        </div>

        <div class="form-group" style="margin-top: 1.25rem;">
            <label for="profile-password-confirm" class="form-label">Confirm New Password</label>
            <input type="password" name="password_confirmation" id="profile-password-confirm" class="form-control" placeholder="Re-enter new password">
        </div>

        <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2.5rem;">
            @php
                $dashRoute = '#';
                if($user->isSuperAdmin()) $dashRoute = route('super_admin.dashboard');
                elseif($user->isAdmin()) $dashRoute = route('admin.dashboard');
                elseif($user->isStaff()) $dashRoute = route('staff.dashboard');
                elseif($user->isCustomer()) $dashRoute = route('customer.dashboard');
            @endphp
            <a href="{{ $dashRoute }}" class="btn btn-outline" style="text-decoration: none;">Back to Dashboard</a>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
    </form>
</div>
@endsection
