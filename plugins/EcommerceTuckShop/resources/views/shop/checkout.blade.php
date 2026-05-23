@extends('layouts.frontend')

@section('title', 'Shop Checkout')

@section('content')
<div class="container" style="max-width: 1000px; margin: 2rem auto; padding: 0 1rem; min-height: 70vh;">
    <!-- Page Title -->
    <div class="glass-panel" style="padding: 2rem; margin-bottom: 2rem; border-radius: var(--radius-md); background: var(--surface-glass); border: 1px solid var(--border-color);">
        <h1 style="font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 2rem; margin-bottom: 0.25rem; color: var(--text-primary);">
            <i class="fa-solid fa-cart-shopping" style="color: var(--primary); margin-right: 0.5rem;"></i> Secure Checkout
        </h1>
        <p style="color: var(--text-secondary); margin: 0;">Complete your order below. Items can be charged directly to your room if you are checked-in.</p>
    </div>

    @if(session('error'))
        <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 2rem; align-items: start;">
        <!-- Left Column: Checkout Form -->
        <form action="{{ route('checkout.process') }}" method="POST" class="glass-panel" style="padding: 2rem; border-radius: var(--radius-md); background: var(--surface-glass); border: 1px solid var(--border-color); display: flex; flex-direction: column; gap: 1.5rem;">
            @csrf

            <!-- Contact Information -->
            <div>
                <h3 style="font-family: 'Outfit', sans-serif; font-size: 1.2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                    <i class="fa-solid fa-user" style="color: var(--primary); margin-right: 0.25rem;"></i> Contact Information
                </h3>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div class="form-group">
                        <label for="customer_name" class="form-label">Full Name</label>
                        <input type="text" name="customer_name" id="customer_name" class="form-control" 
                               value="{{ old('customer_name', auth()->check() ? auth()->user()->name : '') }}" required>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="customer_email" class="form-label">Email Address</label>
                            <input type="email" name="customer_email" id="customer_email" class="form-control" 
                                   value="{{ old('customer_email', auth()->check() ? auth()->user()->email : '') }}">
                        </div>
                        <div class="form-group">
                            <label for="customer_phone" class="form-label">Phone Number</label>
                            <input type="text" name="customer_phone" id="customer_phone" class="form-control" 
                                   value="{{ old('customer_phone', auth()->check() ? auth()->user()->phone : '') }}" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Preference -->
            <div>
                <h3 style="font-family: 'Outfit', sans-serif; font-size: 1.2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                    <i class="fa-solid fa-truck" style="color: var(--primary); margin-right: 0.25rem;"></i> Delivery & Fulfillment
                </h3>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div class="form-group">
                        <label for="delivery_type" class="form-label">Fulfillment Option</label>
                        <select name="delivery_type" id="delivery_type" class="form-control" onchange="updateDeliveryLabels()" required>
                            <option value="room" {{ old('delivery_type') === 'room' || (isset($activeBooking) ? 'selected' : '') }}>Deliver to Room</option>
                            <option value="takeaway" {{ old('delivery_type') === 'takeaway' ? 'selected' : '' }}>Takeaway / Self-Pickup</option>
                            <option value="address" {{ old('delivery_type') === 'address' ? 'selected' : '' }}>Deliver to Custom Address</option>
                        </select>
                    </div>
                    <div class="form-group" id="details_group">
                        <label for="delivery_details" class="form-label" id="details_label">Room Number / Delivery Details</label>
                        <textarea name="delivery_details" id="delivery_details" class="form-control" rows="2" placeholder="e.g. Room 104, Poolside lounge, or shipping address..." required>{{ old('delivery_details', $activeBooking && $activeBooking->room ? 'Room ' . $activeBooking->room->room_number : '') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div>
                <h3 style="font-family: 'Outfit', sans-serif; font-size: 1.2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                    <i class="fa-solid fa-credit-card" style="color: var(--primary); margin-right: 0.25rem;"></i> Payment Method
                </h3>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <!-- Cash Option -->
                    <label style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); cursor: pointer; transition: background 0.2s;">
                        <input type="radio" name="payment_method" value="cash_on_delivery" {{ old('payment_method', 'cash_on_delivery') === 'cash_on_delivery' ? 'checked' : '' }}>
                        <div>
                            <span style="font-weight: 700; color: var(--text-primary); display: block;">Cash / Pay on Delivery</span>
                            <span style="font-size: 0.8rem; color: var(--text-secondary);">Pay with cash or card when your order arrives.</span>
                        </div>
                    </label>

                    <!-- Card Option -->
                    <label style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); cursor: pointer; transition: background 0.2s;">
                        <input type="radio" name="payment_method" value="card" {{ old('payment_method') === 'card' ? 'checked' : '' }}>
                        <div>
                            <span style="font-weight: 700; color: var(--text-primary); display: block;">Online Card Payment (Simulated)</span>
                            <span style="font-size: 0.8rem; color: var(--text-secondary);">Pay immediately using simulated secure credit card processing.</span>
                        </div>
                    </label>

                    <!-- Room Charge Option -->
                    @if($activeBooking)
                        <label style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); cursor: pointer; transition: background 0.2s; background: var(--primary-glow);">
                            <input type="radio" name="payment_method" value="room_charge" {{ old('payment_method') === 'room_charge' ? 'checked' : '' }}>
                            <div>
                                <span style="font-weight: 700; color: var(--text-primary); display: block; color: var(--primary);">
                                    Charge to Room (Room {{ $activeBooking->room->room_number ?? 'N/A' }})
                                </span>
                                <span style="font-size: 0.8rem; color: var(--text-secondary);">Bill this order directly to your reservation. Pay altogether on checkout.</span>
                            </div>
                        </label>
                    @else
                        <div style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); opacity: 0.6; background: #eaeaea; cursor: not-allowed;">
                            <input type="radio" name="payment_method" value="room_charge" disabled>
                            <div>
                                <span style="font-weight: 700; color: var(--text-primary); display: block;">Charge to Room</span>
                                <span style="font-size: 0.8rem; color: var(--text-secondary);">Only available to logged-in guests with active checked-in bookings.</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary btn-block" style="padding: 0.85rem; font-size: 1.1rem; font-weight: 700; justify-content: center; margin-top: 1rem;">
                <i class="fa-solid fa-lock"></i> Place Order
            </button>
        </form>

        <!-- Right Column: Cart Summary -->
        <div class="glass-panel" style="padding: 1.75rem; border-radius: var(--radius-md); background: var(--surface-glass); border: 1px solid var(--border-color); position: sticky; top: 100px;">
            <h3 style="font-family: 'Outfit', sans-serif; font-size: 1.25rem; font-weight: 800; margin-bottom: 1.25rem; color: var(--text-primary); border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                Order Summary
            </h3>

            @php $subtotal = 0; @endphp
            <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.5rem;">
                @foreach($cart as $id => $item)
                    @php $subtotal += $item['price'] * $item['quantity']; @endphp
                    <div style="display: flex; justify-content: space-between; align-items: start; font-size: 0.9rem;">
                        <div>
                            <span style="font-weight: 700; color: var(--text-primary);">{{ $item['name'] }}</span>
                            <span style="font-size: 0.8rem; color: var(--text-secondary); display: block;">
                                {{ $item['quantity'] }} × {{ $currency }}{{ number_format($item['price'], 2) }}
                            </span>
                        </div>
                        <span style="font-weight: 700; color: var(--text-primary);">
                            {{ $currency }}{{ number_format($item['price'] * $item['quantity'], 2) }}
                        </span>
                    </div>
                @endforeach
            </div>

            @php 
                $shippingFeeSetting = (float)\App\Models\Setting::getValue('ecommerce_shipping_fee', '5.00');
                $freeShippingThreshold = (float)\App\Models\Setting::getValue('ecommerce_free_shipping_limit', '50.00');
                $shippingFee = ($subtotal >= $freeShippingThreshold) ? 0.00 : $shippingFeeSetting;
                $taxAmount = $subtotal * ($taxRate / 100);
                $total = $subtotal + $taxAmount + $shippingFee;
            @endphp

            <div style="border-top: 1px solid var(--border-color); padding-top: 1rem; display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1.5rem;">
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem; color: var(--text-secondary);">
                    <span>Subtotal:</span>
                    <span>{{ $currency }}{{ number_format($subtotal, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem; color: var(--text-secondary);">
                    <span>Taxes ({{ $taxRate }}%):</span>
                    <span>{{ $currency }}{{ number_format($taxAmount, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem; color: var(--text-secondary);">
                    <span>Delivery fee:</span>
                    @if($shippingFee > 0)
                        <span style="font-weight: 600; color: var(--text-primary);">{{ $currency }}{{ number_format($shippingFee, 2) }}</span>
                    @else
                        <span style="color: #10b981; font-weight: 600;">FREE</span>
                    @endif
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 800; border-top: 2px solid var(--border-color); padding-top: 0.75rem; margin-top: 0.25rem;">
                    <span style="color: var(--text-primary);">Total Due:</span>
                    <span style="color: var(--primary);">{{ $currency }}{{ number_format($total, 2) }}</span>
                </div>
            </div>

            <div style="background: var(--primary-glow); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); display: flex; gap: 0.5rem; align-items: flex-start;">
                <i class="fa-solid fa-shield-halved" style="color: var(--primary); margin-top: 0.2rem;"></i>
                <p style="font-size: 0.75rem; color: var(--text-secondary); margin: 0; line-height: 1.4;">
                    Your data is secured by {{ \App\Models\Setting::getValue('hotel_name', 'StayFlow') }}'s cloud network. Refunds are managed directly via our front desk.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function updateDeliveryLabels() {
        const option = document.getElementById('delivery_type').value;
        const detailsGroup = document.getElementById('details_group');
        const detailsLabel = document.getElementById('details_label');
        const detailsInput = document.getElementById('delivery_details');

        if (option === 'room') {
            detailsGroup.style.display = 'block';
            detailsLabel.innerText = 'Room Number & Delivery Notes';
            detailsInput.placeholder = 'e.g. Room 204. Please knock soft.';
            detailsInput.required = true;
        } else if (option === 'takeaway') {
            detailsGroup.style.display = 'block';
            detailsLabel.innerText = 'Pickup Notes (Optional)';
            detailsInput.placeholder = 'e.g. Pickup around 7:30 PM.';
            detailsInput.required = false;
        } else if (option === 'address') {
            detailsGroup.style.display = 'block';
            detailsLabel.innerText = 'Full Shipping/Delivery Address';
            detailsInput.placeholder = 'Enter the shipping address for off-resort delivery...';
            detailsInput.required = true;
        }
    }
    
    // Run initially
    document.addEventListener('DOMContentLoaded', function() {
        updateDeliveryLabels();
    });
</script>
@endsection
