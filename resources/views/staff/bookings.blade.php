@extends('layouts.app')

@section('title', 'Guest Bookings')

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 0;">
    <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
        <h2 style="font-size: 1.25rem;"><i class="fa-solid fa-list-check" style="color: var(--primary);"></i> Front Desk Reservations</h2>
    </div>
    
    <div class="table-container" style="border: none;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Guest</th>
                    <th>Room</th>
                    <th>Dates (Check In/Out)</th>
                    <th>Billing Details</th>
                    <th>Payment</th>
                    <th>Reservation Status</th>
                    <th style="text-align: right;">Front Desk Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    <tr>
                        <td>
                            <div style="font-weight: 600;">{{ $booking->customer->name }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">{{ $booking->customer->email }}</div>
                        </td>
                        <td>
                            <div style="font-weight: 700; color: var(--primary);">Room #{{ $booking->room->room_number }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">{{ $booking->room->roomType->name }}</div>
                        </td>
                        <td style="font-size: 0.9rem;">
                            <div><i class="fa-solid fa-arrow-right-to-bracket" style="color: var(--success); font-size: 0.75rem;"></i> {{ $booking->check_in_date->format('M d, Y') }}</div>
                            <div><i class="fa-solid fa-arrow-right-from-bracket" style="color: var(--danger); font-size: 0.75rem;"></i> {{ $booking->check_out_date->format('M d, Y') }}</div>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--success);">${{ number_format($booking->total_price, 2) }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">Tax included</div>
                        </td>
                        <td>
                            @if($booking->payment_status === 'paid')
                                <span class="badge badge-success"><i class="fa-solid fa-credit-card"></i> Paid</span>
                            @elseif($booking->payment_status === 'refunded')
                                <span class="badge badge-danger">Refunded</span>
                            @else
                                <span class="badge badge-warning">Unpaid</span>
                            @endif
                        </td>
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
                                    <span class="badge badge-success" style="opacity:0.6;">Checked Out</span>
                                    @break
                                @case('cancelled')
                                    <span class="badge badge-danger">Cancelled</span>
                                    @break
                            @endswitch
                        </td>
                        <td style="text-align: right;">
                            @if($booking->status === 'confirmed' || $booking->status === 'pending')
                                <form action="{{ route('staff.bookings.check_in', $booking->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">
                                        <i class="fa-solid fa-check"></i> Check In
                                    </button>
                                </form>
                            @elseif($booking->status === 'checked_in')
                                <form action="{{ route('staff.bookings.check_out', $booking->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary" style="font-size: 0.8rem; padding: 0.4rem 0.8rem; background: linear-gradient(135deg, var(--secondary), var(--secondary-light));">
                                        <i class="fa-solid fa-door-closed"></i> Check Out
                                    </button>
                                </form>
                            @else
                                <span style="font-size: 0.85rem; color: var(--text-muted); font-style: italic;">No Action Required</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 4rem 0;">
                            <i class="fa-regular fa-folder-open" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i> No reservations found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
