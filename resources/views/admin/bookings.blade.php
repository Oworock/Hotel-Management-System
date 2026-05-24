@extends('layouts.app')

@section('title', 'Manage Reservations')

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 1.5rem 2rem;">
    <!-- Filters & Search -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;">
        <form action="{{ route('admin.bookings') }}" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; flex-grow: 1; margin: 0;">
            <div class="form-group" style="margin: 0; min-width: 180px;">
                <select name="status" class="form-control form-select" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="checked_in" {{ request('status') === 'checked_in' ? 'selected' : '' }}>Checked In</option>
                    <option value="checked_out" {{ request('status') === 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            
            <div class="form-group" style="margin: 0; min-width: 250px; display: flex; gap: 0.5rem;">
                <input type="text" name="search" class="form-control" placeholder="Search customer name or email..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                @if(request()->anyFilled(['status', 'search']))
                    <a href="{{ route('admin.bookings') }}" class="btn btn-outline" style="display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-rotate-left"></i></a>
                @endif
            </div>
        </form>
        <button onclick="toggleBookingModal(true)" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-plus"></i> Book Room
        </button>
    </div>

    <!-- Bookings Table -->
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Guest</th>
                    <th>Room</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Total Price</th>
                    <th>Discount</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    <tr>
                        <td style="font-weight: 700;">#{{ $booking->id }}</td>
                        <td>
                            <div style="font-weight: 600;">{{ $booking->customer->name ?? 'N/A' }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">{{ $booking->customer->email ?? '' }}</div>
                        </td>
                        <td>
                            @if($booking->room)
                                <div style="font-weight: 600;">Room {{ $booking->room->room_number }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary);">{{ $booking->room->roomType->name ?? '' }}</div>
                            @else
                                <span style="color: var(--danger); font-style: italic;">Deleted Room</span>
                            @endif
                        </td>
                        <td>{{ $booking->check_in_date->format('Y-m-d') }}</td>
                        <td>{{ $booking->check_out_date->format('Y-m-d') }}</td>
                        <td style="font-weight: 700;">${{ number_format($booking->total_price, 2) }}</td>
                        <td>
                            @if($booking->coupon_code)
                                <span class="badge" style="background-color: var(--success-glow); color: var(--success); border: 1px solid var(--success);">
                                    {{ $booking->coupon_code }} (-${{ number_format($booking->discount_amount, 2) }})
                                </span>
                            @else
                                <span style="color: var(--text-secondary); font-size: 0.85rem;">None</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $booking->status === 'checked_in' ? 'success' : ($booking->status === 'checked_out' ? 'secondary' : ($booking->status === 'cancelled' ? 'danger' : 'warning')) }}">
                                {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $booking->payment_status === 'paid' ? 'success' : ($booking->payment_status === 'refunded' ? 'danger' : 'warning') }}">
                                {{ ucfirst($booking->payment_status) }}
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
                                <a href="{{ route('admin.bookings.document', $booking) }}" target="_blank" class="btn btn-sm btn-outline" title="Print {{ $booking->payment_status === 'paid' ? 'Receipt' : 'Invoice' }}">
                                    <i class="fa-solid fa-file-invoice"></i> {{ $booking->payment_status === 'paid' ? 'Receipt' : 'Invoice' }}
                                </a>

                                @if(in_array($booking->status, ['pending', 'confirmed']) && $booking->payment_status === 'paid')
                                    <form action="{{ route('admin.bookings.check_in', $booking->id) }}" method="POST" style="margin: 0;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Check In Guest">
                                            <i class="fa-solid fa-right-to-bracket"></i> Check In
                                        </button>
                                    </form>
                                @endif

                                @if($booking->status === 'checked_in')
                                    <form action="{{ route('admin.bookings.check_out', $booking->id) }}" method="POST" style="margin: 0;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-secondary" title="Check Out Guest">
                                            <i class="fa-solid fa-right-from-bracket"></i> Check Out
                                        </button>
                                    </form>
                                @endif

                                @if(in_array($booking->status, ['pending', 'confirmed']))
                                    <form action="{{ route('admin.bookings.cancel', $booking->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Cancel Booking">
                                            <i class="fa-solid fa-ban"></i> Cancel
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align: center; color: var(--text-secondary); padding: 3rem 0;">
                            <i class="fa-solid fa-calendar-xmark" style="font-size: 2.5rem; margin-bottom: 1rem; color: var(--border-color);"></i>
                            <p>No bookings found matching filters.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $bookings->appends(request()->query())->links() }}
    </div>
</div>

<!-- Modal: Walk-In Booking -->
<div id="booking-modal" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.5); z-index: 10000; align-items: center; justify-content: center; backdrop-filter: blur(8px); overflow-y: auto; padding: 2rem 0;">
    <div class="glass-panel" style="max-width: 600px; width: 90%; padding: 2rem; position: relative; margin: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; margin: 0;">New Walk-In Booking</h2>
            <button onclick="toggleBookingModal(false)" style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: var(--text-secondary);"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form action="{{ route('admin.bookings.walkin') }}" method="POST" style="margin: 0;">
            @csrf
            
            <!-- Customer Type selection -->
            <div class="form-group" style="margin-bottom: 1.2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Customer Type</label>
                <select name="customer_type" id="customer_type_select" class="form-control form-select" onchange="handleCustomerTypeChange()" style="width: 100%;">
                    <option value="existing">Existing Guest</option>
                    <option value="new">Register New Guest</option>
                </select>
            </div>

            <!-- Existing Customer -->
            <div class="form-group" id="existing-customer-group" style="margin-bottom: 1.2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Select Guest</label>
                <select name="customer_id" class="form-control form-select" style="width: 100%;">
                    <option value="">-- Choose Guest --</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->email }})</option>
                    @endforeach
                </select>
            </div>

            <!-- New Customer Details -->
            <div id="new-customer-group" style="display: none; margin-bottom: 1.2rem;">
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="John Doe" style="width: 100%;">
                </div>
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="john@example.com" style="width: 100%;">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Phone Number</label>
                        <input type="text" name="phone" class="form-control" placeholder="+1234567890" style="width: 100%;">
                    </div>
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Nationality</label>
                        <input type="text" name="nationality" class="form-control" placeholder="American" style="width: 100%;">
                    </div>
                </div>
            </div>

            <!-- Room & Dates -->
            <div class="form-group" style="margin-bottom: 1.2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Room Type</label>
                <select name="room_type_id" class="form-control form-select" required style="width: 100%;">
                    <option value="">-- Select Room Type --</option>
                    @foreach($roomTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }} - ${{ number_format($type->base_price, 2) }}/night</option>
                    @endforeach
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.2rem;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Check-in Date</label>
                    <input type="date" name="check_in_date" class="form-control" required min="{{ date('Y-m-d') }}" style="width: 100%;">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Check-out Date</label>
                    <input type="date" name="check_out_date" class="form-control" required min="{{ date('Y-m-d', strtotime('+1 day')) }}" style="width: 100%;">
                </div>
            </div>

            <!-- Optional Coupon -->
            <div class="form-group" style="margin-bottom: 1.2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Coupon Code (Optional)</label>
                <input type="text" name="coupon_code" class="form-control" placeholder="e.g. SAVE10" style="width: 100%;">
            </div>

            <!-- Add-ons -->
            <div class="form-group" style="margin-bottom: 1.2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Add-ons</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 500; cursor: pointer;">
                        <input type="checkbox" name="addons[]" value="airport_shuttle"> Airport Shuttle ($30)
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 500; cursor: pointer;">
                        <input type="checkbox" name="addons[]" value="luxury_spa"> Luxury Spa ($50)
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 500; cursor: pointer;">
                        <input type="checkbox" name="addons[]" value="gourmet_breakfast"> Gourmet Breakfast ($20/night)
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 500; cursor: pointer;">
                        <input type="checkbox" name="addons[]" value="premium_minibar"> Premium Mini-Bar ($40)
                    </label>
                </div>
            </div>

            <!-- Immediate Payment -->
            <div class="form-group" style="margin-bottom: 1.2rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; cursor: pointer;">
                    <input type="checkbox" name="payment_received" value="1" id="payment_received_checkbox" onchange="handlePaymentCheckboxChange()">
                    Record Immediate Payment
                </label>
            </div>

            <div class="form-group" id="payment-method-group" style="display: none; margin-bottom: 1.2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Payment Method</label>
                <select name="payment_method" class="form-control form-select" style="width: 100%;">
                    <option value="Cash">Cash</option>
                    <option value="Card">Card</option>
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;">
                <button type="button" onclick="toggleBookingModal(false)" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Booking</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleBookingModal(show) {
        document.getElementById('booking-modal').style.display = show ? 'flex' : 'none';
    }

    function handleCustomerTypeChange() {
        const select = document.getElementById('customer_type_select');
        const existingGroup = document.getElementById('existing-customer-group');
        const newGroup = document.getElementById('new-customer-group');

        if (select.value === 'existing') {
            existingGroup.style.display = 'block';
            newGroup.style.display = 'none';
        } else {
            existingGroup.style.display = 'none';
            newGroup.style.display = 'block';
        }
    }

    function handlePaymentCheckboxChange() {
        const checkbox = document.getElementById('payment_received_checkbox');
        const paymentGroup = document.getElementById('payment-method-group');
        paymentGroup.style.display = checkbox.checked ? 'block' : 'none';
    }
</script>
@endsection
