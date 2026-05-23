@extends('layouts.app')

@section('title', 'Guest Registry')

@section('content')
<div class="animate-fade-in">
    <!-- Filter Panel -->
    <div class="glass-panel" style="padding: 1.5rem; margin-bottom: 2rem;">
        <form action="{{ route('receptionist.guests') }}" method="GET" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end; margin: 0;">
            <div class="form-group" style="flex: 2; min-width: 250px; margin-bottom: 0;">
                <label for="search" class="form-label" style="font-size: 0.85rem; font-weight: 600;">Search Guest Profile</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Search by name, email or phone..." value="{{ request('search') }}">
            </div>
            
            <div style="display: flex; gap: 0.5rem; margin-bottom: 0;">
                <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.5rem;">
                    <i class="fa-solid fa-magnifying-glass"></i> Search
                </button>
                @if(request()->filled('search'))
                    <a href="{{ route('receptionist.guests') }}" class="btn btn-outline" style="padding: 0.6rem 1.5rem; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Guests Table -->
    <div class="glass-panel" style="padding: 0;">
        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Guest ID</th>
                        <th>Name</th>
                        <th>Email Address</th>
                        <th>Phone Number</th>
                        <th>Nationality</th>
                        <th>Loyalty Points</th>
                        <th>Status</th>
                        <th>Registered Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($guests as $guest)
                        <tr>
                            <td style="font-family: monospace; font-weight: 600;">#{{ $guest->id }}</td>
                            <td style="font-weight: 600;">{{ $guest->name }}</td>
                            <td>{{ $guest->email }}</td>
                            <td>{{ $guest->phone ?: 'N/A' }}</td>
                            <td>{{ $guest->nationality ?: 'N/A' }}</td>
                            <td>
                                <span class="badge badge-info" style="font-weight: 600; padding: 0.25rem 0.5rem;">
                                    <i class="fa-solid fa-star" style="color: #ffd700; margin-right: 0.25rem;"></i> {{ $guest->loyalty_points }} pts
                                </span>
                            </td>
                            <td>
                                @if($guest->is_blacklisted)
                                    <span class="badge badge-danger"><i class="fa-solid fa-user-slash"></i> Blacklisted</span>
                                @else
                                    <span class="badge badge-success">Good Standing</span>
                                @endif
                            </td>
                            <td style="color: var(--text-secondary); font-size: 0.85rem;">
                                {{ $guest->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 3rem 0;">
                                <i class="fa-regular fa-address-book" style="font-size: 2rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i>
                                No registered guests found in system.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($guests->hasPages())
            <div style="padding: 1.5rem; display: flex; justify-content: center; border-top: 1px solid var(--border-color);">
                {{ $guests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
