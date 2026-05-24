@extends('layouts.app')

@section('title', 'My Hotel Reservations')

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 0;">
    <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
        <h2 style="font-size: 1.25rem;"><i class="fa-solid fa-suitcase" style="color: var(--primary);"></i> Your Stay History</h2>
    </div>
    
    <div class="table-container" style="border: none;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Room Details</th>
                    <th>Dates (Check In/Out)</th>
                    <th>Total Price</th>
                    <th>Payment Status</th>
                    <th>Reservation Status</th>
                    <th style="text-align: right;">Online Guest Services</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: var(--primary); font-size: 1.1rem;">Room #{{ $booking->room->room_number }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-secondary); font-weight: 500;">
                                {{ $booking->room->roomType->name }}
                            </div>
                        </td>
                        <td style="font-size: 0.9rem;">
                            <div>
                                <i class="fa-solid fa-arrow-right-to-bracket" style="color: var(--success); font-size: 0.75rem;"></i> 
                                {{ $booking->check_in_date->format('M d, Y') }}
                            </div>
                            <div style="margin-top: 0.25rem;">
                                <i class="fa-solid fa-arrow-right-from-bracket" style="color: var(--danger); font-size: 0.75rem;"></i> 
                                {{ $booking->check_out_date->format('M d, Y') }}
                            </div>
                        </td>
                        <td style="font-weight: 600; color: var(--success);">
                            ${{ number_format($booking->total_price, 2) }}
                        </td>
                        <td>
                            @if($booking->payment_status === 'paid')
                                <span class="badge badge-success"><i class="fa-solid fa-credit-card"></i> Paid</span>
                            @else
                                <div style="display: flex; flex-direction: column; gap: 0.25rem; align-items: flex-start;">
                                    <span class="badge badge-warning">Unpaid</span>
                                    <a href="{{ route('customer.payment', $booking->id) }}" class="btn btn-primary" style="font-size: 0.7rem; padding: 0.25rem 0.5rem; border-radius: 4px;">
                                        Pay Now
                                    </a>
                                </div>
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
                                    <span class="badge badge-success" style="opacity: 0.6;">Checked Out</span>
                                    @break
                                @case('cancelled')
                                    <span class="badge badge-danger">Cancelled</span>
                                    @break
                            @endswitch
                        </td>
                        <td style="text-align: right;">
                            @php
                                $today = \Carbon\Carbon::today();
                                $canCheckIn = ($booking->status === 'confirmed' || $booking->status === 'pending') && 
                                              $booking->payment_status === 'paid' && 
                                              $today->greaterThanOrEqualTo($booking->check_in_date) && 
                                              $today->lessThan($booking->check_out_date);
                                              
                                $canCheckOut = ($booking->status === 'checked_in');
                            @endphp

                            <a href="{{ route('customer.bookings.document', $booking) }}" target="_blank" class="btn btn-outline" style="font-size: 0.8rem; padding: 0.4rem 0.8rem; margin-right: 0.35rem;">
                                <i class="fa-solid fa-file-invoice"></i> {{ $booking->payment_status === 'paid' ? 'Receipt' : 'Invoice' }}
                            </a>

                            @if($canCheckIn)
                                <form action="{{ route('customer.bookings.check_in', $booking->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">
                                        <i class="fa-solid fa-hotel"></i> Self Check-In
                                    </button>
                                </form>
                            @elseif($canCheckOut)
                                <form action="{{ route('customer.bookings.check_out', $booking->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary" style="font-size: 0.8rem; padding: 0.4rem 0.8rem; background: linear-gradient(135deg, var(--secondary), var(--secondary-light));">
                                        <i class="fa-solid fa-door-closed"></i> Self Check-Out
                                    </button>
                                </form>
                            @else
                                @if($booking->status === 'confirmed' && $today->lt($booking->check_in_date))
                                    <span style="font-size: 0.8rem; color: var(--text-muted); font-style: italic;">
                                        Check-in opens on stay date
                                    </span>
                                @elseif($booking->status === 'checked_out')
                                    <span style="font-size: 0.8rem; color: var(--text-muted);">
                                        Thank you for staying!
                                    </span>
                                @else
                                    <span style="font-size: 0.8rem; color: var(--text-muted); font-style: italic;">
                                        Services Unavailable
                                    </span>
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 4rem 0;">
                            <i class="fa-solid fa-suitcase" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i> You don't have any bookings yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
