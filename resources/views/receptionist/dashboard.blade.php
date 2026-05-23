@extends('layouts.app')

@section('title', 'Receptionist Dashboard')

@section('content')
<div class="animate-fade-in">
    <!-- Quick Statistics Grid -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <!-- Active Check-ins -->
        <div class="glass-panel stat-card" style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem;">
            <div>
                <p style="color: var(--text-secondary); font-size: 0.875rem; font-weight: 600; margin: 0;">Active Check-ins</p>
                <div class="stat-value" style="font-size: 2.2rem; font-weight: 700; color: var(--primary); margin-top: 0.5rem;">
                    {{ $activeCheckins }}
                </div>
            </div>
            <div class="stat-icon" style="font-size: 2rem; color: var(--primary); opacity: 0.8;"><i class="fa-solid fa-user-check"></i></div>
        </div>

        <!-- Arrivals Today -->
        <div class="glass-panel stat-card" style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem;">
            <div>
                <p style="color: var(--text-secondary); font-size: 0.875rem; font-weight: 600; margin: 0;">Arrivals Today</p>
                <div class="stat-value" style="font-size: 2.2rem; font-weight: 700; color: var(--info); margin-top: 0.5rem;">
                    {{ $arrivalsToday }}
                </div>
            </div>
            <div class="stat-icon" style="font-size: 2rem; color: var(--info); opacity: 0.8;"><i class="fa-solid fa-plane-arrival"></i></div>
        </div>

        <!-- Departures Today -->
        <div class="glass-panel stat-card" style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem;">
            <div>
                <p style="color: var(--text-secondary); font-size: 0.875rem; font-weight: 600; margin: 0;">Departures Today</p>
                <div class="stat-value" style="font-size: 2.2rem; font-weight: 700; color: var(--warning); margin-top: 0.5rem;">
                    {{ $departuresToday }}
                </div>
            </div>
            <div class="stat-icon" style="font-size: 2rem; color: var(--warning); opacity: 0.8;"><i class="fa-solid fa-plane-departure"></i></div>
        </div>

        <!-- Available Rooms -->
        <div class="glass-panel stat-card" style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem;">
            <div>
                <p style="color: var(--text-secondary); font-size: 0.875rem; font-weight: 600; margin: 0;">Available Rooms</p>
                <div class="stat-value" style="font-size: 2.2rem; font-weight: 700; color: var(--success); margin-top: 0.5rem;">
                    {{ $availableRooms }}
                </div>
            </div>
            <div class="stat-icon" style="font-size: 2rem; color: var(--success); opacity: 0.8;"><i class="fa-solid fa-door-open"></i></div>
        </div>
    </div>

    <!-- Room Status Summary -->
    <div class="glass-panel" style="margin-bottom: 2rem; padding: 1.5rem;">
        <h3 style="font-size: 1.15rem; margin-bottom: 1.25rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-chart-pie" style="color: var(--primary);"></i> Room Occupancy & Cleaning Status
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
            <!-- Available -->
            <div style="background: var(--surface-hover); padding: 1rem; border-radius: var(--radius-sm); border-left: 4px solid var(--success);">
                <span style="font-size: 0.8rem; color: var(--text-secondary); font-weight: 600;">Available (Clean)</span>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--success); margin-top: 0.25rem;">{{ $availableRooms }}</div>
            </div>
            <!-- Booked -->
            <div style="background: var(--surface-hover); padding: 1rem; border-radius: var(--radius-sm); border-left: 4px solid var(--primary);">
                <span style="font-size: 0.8rem; color: var(--text-secondary); font-weight: 600;">Occupied</span>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary); margin-top: 0.25rem;">{{ $bookedRooms }}</div>
            </div>
            <!-- Dirty -->
            <div style="background: var(--surface-hover); padding: 1rem; border-radius: var(--radius-sm); border-left: 4px solid var(--warning);">
                <span style="font-size: 0.8rem; color: var(--text-secondary); font-weight: 600;">Dirty (Needs Cleaning)</span>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--warning); margin-top: 0.25rem;">{{ $dirtyRooms }}</div>
            </div>
            <!-- Maintenance -->
            <div style="background: var(--surface-hover); padding: 1rem; border-radius: var(--radius-sm); border-left: 4px solid var(--danger);">
                <span style="font-size: 0.8rem; color: var(--text-secondary); font-weight: 600;">Out of Order</span>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--danger); margin-top: 0.25rem;">{{ $maintenanceRooms }}</div>
            </div>
        </div>
    </div>

    <!-- Action Panels for Today's Bookings -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem;">
        <!-- Today's Arrivals -->
        <div class="glass-panel" style="display: flex; flex-direction: column; padding: 1.5rem;">
            <h3 style="font-size: 1.15rem; margin-bottom: 1rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-plane-arrival" style="color: var(--info);"></i> Arriving Guests Today
            </h3>
            
            <div class="table-container" style="flex: 1; border: none; margin-top: 0.5rem;">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Guest</th>
                            <th>Room / Type</th>
                            <th>Payment</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($todayArrivals as $booking)
                            <tr>
                                <td>
                                    <div style="font-weight: 600;">{{ $booking->customer->name }}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-secondary);">{{ $booking->customer->email }}</div>
                                </td>
                                <td>
                                    <div>#{{ $booking->room->room_number }}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-secondary);">{{ $booking->room->roomType->name }}</div>
                                </td>
                                <td>
                                    @if($booking->payment_status === 'paid')
                                        <span class="badge badge-success">Paid</span>
                                    @else
                                        <span class="badge badge-danger">Unpaid (${{ number_format($booking->total_price, 2) }})</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    @if($booking->payment_status === 'paid')
                                        <form action="{{ route('receptionist.bookings.check_in', $booking->id) }}" method="POST" style="margin: 0; display: inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary" style="font-size: 0.75rem; padding: 0.35rem 0.75rem;">
                                                <i class="fa-solid fa-key"></i> Check In
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('receptionist.bookings.pay', $booking->id) }}" method="POST" style="margin: 0; display: inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" style="font-size: 0.75rem; padding: 0.35rem 0.75rem;">
                                                <i class="fa-solid fa-credit-card"></i> Pay & Confirm
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 3rem 0;">
                                    <i class="fa-regular fa-calendar" style="font-size: 2rem; margin-bottom: 0.75rem; display: block; opacity: 0.5;"></i>
                                    No pending arrivals today.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Today's Departures -->
        <div class="glass-panel" style="display: flex; flex-direction: column; padding: 1.5rem;">
            <h3 style="font-size: 1.15rem; margin-bottom: 1rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-plane-departure" style="color: var(--warning);"></i> Departing Guests Today
            </h3>
            
            <div class="table-container" style="flex: 1; border: none; margin-top: 0.5rem;">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Guest</th>
                            <th>Room / Type</th>
                            <th>Stay Dates</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($todayDepartures as $booking)
                            <tr>
                                <td>
                                    <div style="font-weight: 600;">{{ $booking->customer->name }}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-secondary);">{{ $booking->customer->email }}</div>
                                </td>
                                <td>
                                    <div>#{{ $booking->room->room_number }}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-secondary);">{{ $booking->room->roomType->name }}</div>
                                </td>
                                <td style="font-size: 0.8rem; color: var(--text-secondary);">
                                    {{ $booking->check_in_date->format('M d') }} - {{ $booking->check_out_date->format('M d') }}
                                </td>
                                <td style="text-align: right;">
                                    <form action="{{ route('receptionist.bookings.check_out', $booking->id) }}" method="POST" style="margin: 0; display: inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline" style="border-color: var(--warning); color: var(--warning); font-size: 0.75rem; padding: 0.35rem 0.75rem;">
                                            <i class="fa-solid fa-door-closed"></i> Check Out
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 3rem 0;">
                                    <i class="fa-regular fa-calendar-check" style="font-size: 2rem; margin-bottom: 0.75rem; display: block; opacity: 0.5;"></i>
                                    No pending departures today.
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
