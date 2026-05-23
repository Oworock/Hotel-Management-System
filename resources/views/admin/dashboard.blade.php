@extends('layouts.app')

@section('title', 'Business Analytics & CMS')

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 1.5rem 2rem 2.5rem 2rem;">
    <!-- Business Analytics Title -->
    <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem; font-weight: 700; color: var(--text-primary);">
        <i class="fa-solid fa-chart-line" style="color: var(--primary); margin-right: 0.5rem;"></i> Business Analytics
    </h2>

    <!-- Metric Cards -->
        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
            <div class="glass-panel stat-card">
                <div>
                    <p style="color: var(--text-secondary); font-size: 0.875rem; font-weight: 600;">Total Business Earnings</p>
                    <div class="stat-value" style="color: var(--success);">
                        ${{ number_format($totalEarnings, 2) }}
                    </div>
                </div>
                <div class="stat-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
            </div>
            
            <div class="glass-panel stat-card">
                <div>
                    <p style="color: var(--text-secondary); font-size: 0.875rem; font-weight: 600;">Room Occupancy Rate</p>
                    <div class="stat-value">{{ $occupancyRate }}%</div>
                </div>
                <div class="stat-icon"><i class="fa-solid fa-percent"></i></div>
            </div>
            
            <div class="glass-panel stat-card">
                <div>
                    <p style="color: var(--text-secondary); font-size: 0.875rem; font-weight: 600;">Staff Clocked-In</p>
                    <div class="stat-value">{{ $activeStaffCount }}</div>
                </div>
                <div class="stat-icon"><i class="fa-solid fa-user-clock"></i></div>
            </div>

            <div class="glass-panel stat-card">
                <div>
                    <p style="color: var(--text-secondary); font-size: 0.875rem; font-weight: 600;">Avg Booking Revenue</p>
                    <div class="stat-value" style="color: var(--primary); font-size: 1.8rem; font-weight: 700; margin-top: 0.5rem;">${{ number_format($averageRevenue, 2) }}</div>
                </div>
                <div class="stat-icon"><i class="fa-solid fa-money-bill-wave"></i></div>
            </div>
            
            <div class="glass-panel stat-card">
                <div>
                    <p style="color: var(--text-secondary); font-size: 0.875rem; font-weight: 600;">Avg Stay Duration</p>
                    <div class="stat-value" style="font-size: 1.8rem; font-weight: 700; margin-top: 0.5rem;">{{ $averageDuration }} {{ $averageDuration == 1 ? 'night' : 'nights' }}</div>
                </div>
                <div class="stat-icon"><i class="fa-regular fa-moon"></i></div>
            </div>
        </div>

        <!-- Layout Breakdown Grid -->
        <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 2rem; margin-top: 2rem;">
            <!-- Room Status Chart (HTML progress bars & stats) -->
            <div class="glass-panel" style="display: flex; flex-direction: column; gap: 1.5rem;">
                <h3 style="font-size: 1.25rem;"><i class="fa-solid fa-chart-pie" style="color: var(--primary);"></i> Room Occupancy Status</h3>
                
                <div style="display: flex; flex-direction: column; gap: 1.25rem; margin-top: 1rem;">
                    <!-- Available -->
                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.9rem; font-weight: 600; margin-bottom: 0.5rem;">
                            <span>Available Rooms</span>
                            <span>{{ $roomStatuses['available'] }} / {{ $totalRooms }}</span>
                        </div>
                        <div style="width: 100%; height: 8px; background-color: var(--border-color); border-radius: 4px; overflow: hidden;">
                            <div style="width: {{ $totalRooms > 0 ? ($roomStatuses['available'] / $totalRooms) * 100 : 0 }}%; height: 100%; background: var(--success);"></div>
                        </div>
                    </div>
                    
                    <!-- Booked -->
                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.9rem; font-weight: 600; margin-bottom: 0.5rem;">
                            <span>Occupied / Booked</span>
                            <span>{{ $roomStatuses['booked'] }} / {{ $totalRooms }}</span>
                        </div>
                        <div style="width: 100%; height: 8px; background-color: var(--border-color); border-radius: 4px; overflow: hidden;">
                            <div style="width: {{ $totalRooms > 0 ? ($roomStatuses['booked'] / $totalRooms) * 100 : 0 }}%; height: 100%; background: var(--primary);"></div>
                        </div>
                    </div>
                    
                    <!-- Dirty -->
                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.9rem; font-weight: 600; margin-bottom: 0.5rem;">
                            <span>Housekeeping Required (Dirty)</span>
                            <span>{{ $roomStatuses['dirty'] }} / {{ $totalRooms }}</span>
                        </div>
                        <div style="width: 100%; height: 8px; background-color: var(--border-color); border-radius: 4px; overflow: hidden;">
                            <div style="width: {{ $totalRooms > 0 ? ($roomStatuses['dirty'] / $totalRooms) * 100 : 0 }}%; height: 100%; background: var(--warning);"></div>
                        </div>
                    </div>
                    
                    <!-- Maintenance -->
                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.9rem; font-weight: 600; margin-bottom: 0.5rem;">
                            <span>Under Maintenance</span>
                            <span>{{ $roomStatuses['maintenance'] }} / {{ $totalRooms }}</span>
                        </div>
                        <div style="width: 100%; height: 8px; background-color: var(--border-color); border-radius: 4px; overflow: hidden;">
                            <div style="width: {{ $totalRooms > 0 ? ($roomStatuses['maintenance'] / $totalRooms) * 100 : 0 }}%; height: 100%; background: var(--danger);"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Bookings Table -->
            <div class="glass-panel" style="display: flex; flex-direction: column;">
                <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem;"><i class="fa-solid fa-list" style="color: var(--primary);"></i> Recent Guest Bookings</h3>
                
                <div class="table-container" style="flex: 1; border: none;">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Guest</th>
                                <th>Room</th>
                                <th>Type</th>
                                <th>Stay Dates</th>
                                <th>Bill</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings as $booking)
                                <tr>
                                    <td style="font-weight: 600;">{{ $booking->customer->name }}</td>
                                    <td>#{{ $booking->room->room_number }}</td>
                                    <td>{{ $booking->room->roomType->name }}</td>
                                    <td style="font-size: 0.85rem; color: var(--text-secondary);">
                                        {{ $booking->check_in_date->format('M d') }} - {{ $booking->check_out_date->format('M d') }}
                                    </td>
                                    <td style="font-weight: 600; color: var(--success);">${{ number_format($booking->total_price, 2) }}</td>
                                    <td>
                                        @switch($booking->status)
                                            @case('pending')
                                                <span class="badge badge-warning">Pending</span>
                                                @break
                                            @case('confirmed')
                                                <span class="badge badge-info">Confirmed</span>
                                                @break
                                            @case('checked_in')
                                                <span class="badge badge-success">Checked In</span>
                                                @break
                                            @case('checked_out')
                                                <span class="badge badge-success" style="opacity: 0.65;">Checked Out</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge badge-danger">Cancelled</span>
                                                @break
                                        @endswitch
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 3rem 0;">
                                        <i class="fa-regular fa-folder-open" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i> No bookings found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Housekeeping & Staff Shifts Live Lists -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem;">
            <!-- Active Shifts -->
            <div class="glass-panel" style="display: flex; flex-direction: column;">
                <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem;"><i class="fa-solid fa-user-clock" style="color: var(--primary);"></i> Clocked-In Staff</h3>
                <div class="table-container" style="flex: 1; border: none;">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Staff Member</th>
                                <th>Clock In Time</th>
                                <th>Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeShifts as $shift)
                                <tr>
                                    <td style="font-weight: 600;">{{ $shift->user->name }}</td>
                                    <td>{{ $shift->clock_in_at->format('M d, H:i') }}</td>
                                    <td>
                                        <span class="badge badge-success">Active ({{ $shift->clock_in_at->diffForHumans(null, true) }})</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="text-align: center; color: var(--text-muted); padding: 2rem 0;">
                                        No staff currently clocked in.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Housekeeping Monitor -->
            <div class="glass-panel" style="display: flex; flex-direction: column;">
                <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem;"><i class="fa-solid fa-broom" style="color: var(--warning);"></i> Housekeeping Monitor</h3>
                <div class="table-container" style="flex: 1; border: none;">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Room Number</th>
                                <th>Room Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dirtyRooms as $room)
                                <tr>
                                    <td style="font-weight: 600;">Room #{{ $room->room_number }}</td>
                                    <td>{{ $room->roomType->name }}</td>
                                    <td>
                                        <form action="{{ route('admin.rooms.status', $room->id) }}" method="POST" style="margin: 0;">
                                            @csrf
                                            <input type="hidden" name="status" value="available">
                                            <button type="submit" class="btn btn-sm btn-success" style="font-size: 0.8rem; padding: 0.25rem 0.5rem; display: inline-flex; align-items: center; gap: 0.25rem;">
                                                <i class="fa-solid fa-check"></i> Mark Clean
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="text-align: center; color: var(--text-muted); padding: 2rem 0;">
                                        All rooms are clean!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
            </div>
        </div>
    </div>
</div>
@endsection
