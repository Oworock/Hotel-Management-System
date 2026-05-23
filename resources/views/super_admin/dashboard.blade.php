@extends('layouts.app')

@section('title', 'Super Admin Analytics Dashboard')

@section('content')
<div class="animate-fade-in">
    <!-- Super Admin Welcome Banner -->
    <div class="glass-panel" style="padding: 2rem; margin-bottom: 2rem; border-left: 5px solid var(--primary); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
        <div>
            <h2 style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.5rem;">System Overview Dashboard</h2>
            <p style="color: var(--text-secondary); margin: 0;">Monitor global hotel performance, billing statistics, and live user metrics.</p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('super_admin.settings') }}" class="btn btn-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                <i class="fa-solid fa-gears"></i> System Config
            </a>
            <a href="{{ route('super_admin.users') }}" class="btn btn-outline" style="text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                <i class="fa-solid fa-user-gear"></i> Manage Users
            </a>
        </div>
    </div>

    <!-- Advanced Stat Cards -->
    <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="glass-panel stat-card" style="position: relative; overflow: hidden; display: flex; justify-content: space-between; align-items: center; padding: 1.5rem;">
            <div>
                <p style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 0.5rem 0;">Total Earnings</p>
                <div class="stat-value" style="font-size: 1.75rem; font-weight: 800;">${{ number_format($totalEarnings, 2) }}</div>
            </div>
            <div class="stat-icon" style="background: var(--success-glow); color: var(--success); width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;"><i class="fa-solid fa-hand-holding-dollar"></i></div>
        </div>
        
        <div class="glass-panel stat-card" style="position: relative; overflow: hidden; display: flex; justify-content: space-between; align-items: center; padding: 1.5rem;">
            <div>
                <p style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 0.5rem 0;">Occupancy Rate</p>
                <div class="stat-value" style="font-size: 1.75rem; font-weight: 800;">{{ $occupancyRate }}%</div>
            </div>
            <div class="stat-icon" style="background: var(--primary-glow); color: var(--primary); width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;"><i class="fa-solid fa-door-closed"></i></div>
        </div>

        <div class="glass-panel stat-card" style="position: relative; overflow: hidden; display: flex; justify-content: space-between; align-items: center; padding: 1.5rem;">
            <div>
                <p style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 0.5rem 0;">Total Bookings</p>
                <div class="stat-value" style="font-size: 1.75rem; font-weight: 800;">{{ $totalBookings }}</div>
            </div>
            <div class="stat-icon" style="background: var(--secondary-glow); color: var(--secondary); width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;"><i class="fa-solid fa-hotel"></i></div>
        </div>

        <div class="glass-panel stat-card" style="position: relative; overflow: hidden; display: flex; justify-content: space-between; align-items: center; padding: 1.5rem;">
            <div>
                <p style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 0.5rem 0;">System Users</p>
                <div class="stat-value" style="font-size: 1.75rem; font-weight: 800;">{{ $usersCount }}</div>
            </div>
            <div class="stat-icon" style="background: var(--info-glow); color: var(--info); width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;"><i class="fa-solid fa-users"></i></div>
        </div>
    </div>

    <!-- Analytics Graphs & Breakdowns -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
        <!-- Booking Status Breakdown -->
        <div class="glass-panel" style="padding: 1.5rem;">
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem; color: var(--text-primary);">
                <i class="fa-solid fa-chart-pie" style="color: var(--primary); margin-right: 0.5rem;"></i> Booking Status Breakdown
            </h3>
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                @php
                    $statuses = [
                        'pending' => ['label' => 'Pending Confirmation', 'color' => 'var(--warning)'],
                        'confirmed' => ['label' => 'Paid & Confirmed', 'color' => 'var(--primary)'],
                        'checked_in' => ['label' => 'Active Checked-In', 'color' => 'var(--success)'],
                        'checked_out' => ['label' => 'Checked Out / Past', 'color' => 'var(--text-muted)'],
                        'cancelled' => ['label' => 'Cancelled Bookings', 'color' => 'var(--danger)']
                    ];
                @endphp
                @foreach($statuses as $statusKey => $statusDetail)
                    @php
                        $count = $bookingsGrouped[$statusKey] ?? 0;
                        $percentage = $totalBookings > 0 ? round(($count / $totalBookings) * 100) : 0;
                    @endphp
                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 0.4rem;">
                            <span style="font-weight: 600; color: var(--text-primary);">{{ $statusDetail['label'] }}</span>
                            <span style="font-weight: 700; color: var(--text-primary);">{{ $count }} ({{ $percentage }}%)</span>
                        </div>
                        <div style="width: 100%; height: 8px; background: rgba(255, 255, 255, 0.05); border-radius: 4px; overflow: hidden;">
                            <div style="width: {{ $percentage }}%; height: 100%; background: {{ $statusDetail['color'] }}; border-radius: 4px;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Live Occupancy & Quick System Links -->
        <div class="glass-panel" style="padding: 1.5rem; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;">
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem; color: var(--text-primary); width: 100%; text-align: left;">
                <i class="fa-solid fa-door-open" style="color: var(--secondary); margin-right: 0.5rem;"></i> Live Room Occupancy Gauge
            </h3>
            
            <div style="position: relative; width: 160px; height: 160px; display: flex; align-items: center; justify-content: center; margin: 1rem 0;">
                <div style="width: 100%; height: 100%; border-radius: 50%; background: conic-gradient(var(--primary) {{ $occupancyRate * 3.6 }}deg, rgba(255,255,255,0.05) 0deg); display: flex; align-items: center; justify-content: center;">
                    <div style="width: 120px; height: 120px; border-radius: 50%; background: var(--surface); display: flex; flex-direction: column; align-items: center; justify-content: center; box-shadow: var(--shadow-sm);">
                        <span style="font-size: 2rem; font-weight: 800; color: var(--primary);">{{ $occupancyRate }}%</span>
                        <span style="font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase; font-weight: 600; letter-spacing: 0.05em;">Occupied</span>
                    </div>
                </div>
            </div>

            <div style="width: 100%; margin-top: 1rem; border-top: 1px solid var(--border-color); padding-top: 1rem; text-align: left; display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div style="padding: 0.5rem; text-align: center; border: 1px solid var(--border-color); border-radius: var(--radius-sm);">
                    <div style="font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase; font-weight: 600;">Active Staff</div>
                    <div style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin-top: 0.25rem;">{{ $activeStaffCount }}</div>
                </div>
                <div style="padding: 0.5rem; text-align: center; border: 1px solid var(--border-color); border-radius: var(--radius-sm);">
                    <div style="font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase; font-weight: 600;">Hotel Admins</div>
                    <div style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin-top: 0.25rem;">{{ $adminCount }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent System-Wide Bookings -->
    <div class="glass-panel" style="padding: 1.5rem; margin-bottom: 2rem;">
        <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.25rem; color: var(--text-primary);">
            <i class="fa-solid fa-list-check" style="color: var(--primary); margin-right: 0.5rem;"></i> Recent System Bookings
        </h3>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Guest Name</th>
                        <th>Room Info</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentBookings as $booking)
                        <tr>
                            <td style="font-weight: 600;">{{ $booking->customer->name ?? 'Guest' }}</td>
                            <td>Room {{ $booking->room->room_number ?? 'Unassigned' }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('M d, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->check_out_date)->format('M d, Y') }}</td>
                            <td style="font-weight: 700;">${{ number_format($booking->total_price, 2) }}</td>
                            <td>
                                <span class="badge @if($booking->status == 'checked_in' || $booking->status == 'confirmed') badge-success @elseif($booking->status == 'pending') badge-warning @else badge-muted @endif">
                                    {{ str_replace('_', ' ', $booking->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-secondary);">No platform reservations loaded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
