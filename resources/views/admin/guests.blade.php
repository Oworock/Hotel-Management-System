@extends('layouts.app')

@section('title', 'Manage Guests')

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 1.5rem 2rem;">
    <!-- Header & Search -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;">
        <div>
            <h2 style="font-size: 1.5rem; margin-bottom: 0.25rem;"><i class="fa-solid fa-user-group" style="color: var(--primary); margin-right: 0.5rem;"></i> Guest Roster</h2>
            <p style="color: var(--text-secondary); font-size: 0.875rem;">View and manage guest profiles, contact details, loyalty metrics, and system access.</p>
        </div>
        
        <form action="{{ route('admin.guests') }}" method="GET" style="display: flex; gap: 0.5rem; width: 100%; max-width: 400px; margin: 0;">
            <div class="form-group" style="margin: 0; flex-grow: 1; display: flex; gap: 0.5rem;">
                <input type="text" name="search" class="form-control" placeholder="Search by name, email, phone, or nationality..." value="{{ request('search') }}" style="width: 100%;">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                @if(request()->filled('search'))
                    <a href="{{ route('admin.guests') }}" class="btn btn-outline" style="display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-rotate-left"></i></a>
                @endif
            </div>
        </form>
    </div>

    <!-- Guests Table -->
    <div class="table-container" style="border: none; box-shadow: var(--shadow-sm); border-radius: var(--radius-md); overflow: hidden;">
        <table class="table">
            <thead>
                <tr>
                    <th>Guest ID</th>
                    <th>Name / Email</th>
                    <th>Phone</th>
                    <th>Nationality</th>
                    <th>Loyalty Points</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($guests as $guest)
                    <tr style="{{ $guest->is_blacklisted ? 'background-color: var(--danger-glow);' : '' }}">
                        <td style="font-weight: 700;">#{{ $guest->id }}</td>
                        <td>
                            <div style="font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                                {{ $guest->name }}
                                @if($guest->is_blacklisted)
                                    <span class="badge badge-danger" style="font-size: 0.7rem; padding: 0.1rem 0.4rem;"><i class="fa-solid fa-user-slash"></i> Blacklisted</span>
                                @endif
                            </div>
                            <div style="font-size: 0.8rem; color: var(--text-secondary);">{{ $guest->email }}</div>
                        </td>
                        <td>
                            @if($guest->phone)
                                <span style="font-family: monospace;">{{ $guest->phone }}</span>
                            @else
                                <span style="color: var(--text-muted); font-style: italic; font-size: 0.85rem;">Not provided</span>
                            @endif
                        </td>
                        <td>
                            @if($guest->nationality)
                                <span style="font-weight: 500;">{{ $guest->nationality }}</span>
                            @else
                                <span style="color: var(--text-muted); font-style: italic; font-size: 0.85rem;">Not provided</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.4rem; font-weight: 600;">
                                <i class="fa-solid fa-star" style="color: var(--warning);"></i>
                                <span>{{ $guest->loyalty_points }}</span>
                            </div>
                        </td>
                        <td>
                            @if($guest->is_blacklisted)
                                <span class="badge badge-danger">Restricted</span>
                            @else
                                <span class="badge badge-success">Active</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <form action="{{ route('admin.guests.blacklist', $guest->id) }}" method="POST" style="margin: 0; display: inline-block;" onsubmit="return confirm('Are you sure you want to change the restriction status for this guest?')">
                                @csrf
                                @if($guest->is_blacklisted)
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fa-solid fa-user-check"></i> Whitelist
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fa-solid fa-user-slash"></i> Blacklist
                                    </button>
                                @endif
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 4rem 0;">
                            <i class="fa-solid fa-users-slash" style="font-size: 3rem; margin-bottom: 1rem; color: var(--border-color); display: block;"></i>
                            <p>No guest records found.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($guests->hasPages())
        <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
            {{ $guests->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
