@extends('layouts.app')

@section('title', 'Configure Reservation')

@section('content')
<div style="display: grid; grid-template-columns: 1.6fr 1.4fr; gap: 2rem; max-width: 1100px; margin: 0 auto;" class="animate-fade-in">
    <!-- Left Column: Booking Form Details -->
    <div class="glass-panel" style="display: flex; flex-direction: column; gap: 1.5rem;">
        <h3 style="font-size: 1.25rem;"><i class="fa-solid fa-calendar-days" style="color: var(--primary);"></i> Reservation Details</h3>
        
        <form action="{{ route('customer.book.process') }}" method="POST">
            @csrf
            <input type="hidden" name="room_type_id" value="{{ $roomType->id }}">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="check_in_date" class="form-label">Check-In Date</label>
                    <input type="date" name="check_in_date" id="check_in_date" class="form-control" value="{{ $checkIn }}" min="{{ date('Y-m-d') }}" required>
                </div>
                
                <div class="form-group">
                    <label for="check_out_date" class="form-label">Check-Out Date</label>
                    <input type="date" name="check_out_date" id="check_out_date" class="form-control" value="{{ $checkOut }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="guests" class="form-label">Number of Guests</label>
                <select name="guests" id="guests" class="form-control form-select" required>
                    @for($i = 1; $i <= $roomType->capacity; $i++)
                        <option value="{{ $i }}">{{ $i }} Guest{{ $i > 1 ? 's' : '' }}</option>
                    @endfor
                </select>
            </div>

            @php($guest = auth()->user())
            <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px dashed var(--border-color);">
                <h3 style="font-size: 1.05rem; margin-bottom: 0.5rem;"><i class="fa-solid fa-id-card" style="color: var(--primary);"></i> Guest Stay Profile</h3>
                <p style="color: var(--text-secondary); font-size: 0.85rem; line-height: 1.6; margin-bottom: 1rem;">Complete these details once so reception can prepare your arrival, verify your identity, and contact someone in an emergency.</p>

                <div class="form-row">
                    <div class="form-group">
                        <label for="title" class="form-label">Title</label>
                        <select name="title" id="title" class="form-control form-select">
                            <option value="">Select title</option>
                            @foreach(['Mr', 'Mrs', 'Ms', 'Miss', 'Dr', 'Prof'] as $title)
                                <option value="{{ $title }}" {{ old('title', $guest->title) === $title ? 'selected' : '' }}>{{ $title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="gender" class="form-label">Gender</label>
                        <select name="gender" id="gender" class="form-control form-select">
                            <option value="">Prefer not to say</option>
                            @foreach(['female' => 'Female', 'male' => 'Male', 'non_binary' => 'Non-binary', 'other' => 'Other'] as $value => $label)
                                <option value="{{ $value }}" {{ old('gender', $guest->gender) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" value="{{ old('date_of_birth', $guest->date_of_birth) }}" max="{{ date('Y-m-d', strtotime('-1 day')) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="nationality" class="form-label">Nationality</label>
                        <input type="text" name="nationality" id="nationality" class="form-control" value="{{ old('nationality', $guest->nationality) }}" placeholder="e.g. Nigerian" required>
                    </div>
                </div>

                @include('partials.phone-input', ['field' => 'phone', 'label' => 'Mobile Number', 'value' => old('phone', $guest->phone), 'required' => true])

                <div class="form-row">
                    <div class="form-group">
                        <label for="country_of_residence" class="form-label">Country of Residence</label>
                        <input type="text" name="country_of_residence" id="country_of_residence" class="form-control" value="{{ old('country_of_residence', $guest->country_of_residence) }}" placeholder="Country where you live" required>
                    </div>
                    <div class="form-group">
                        <label for="city" class="form-label">City</label>
                        <input type="text" name="city" id="city" class="form-control" value="{{ old('city', $guest->city) }}" placeholder="City" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address_line1" class="form-label">Residential Address</label>
                    <input type="text" name="address_line1" id="address_line1" class="form-control" value="{{ old('address_line1', $guest->address_line1) }}" placeholder="Street address" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="address_line2" class="form-label">Apartment / Suite</label>
                        <input type="text" name="address_line2" id="address_line2" class="form-control" value="{{ old('address_line2', $guest->address_line2) }}" placeholder="Optional">
                    </div>
                    <div class="form-group">
                        <label for="state" class="form-label">State / Province</label>
                        <input type="text" name="state" id="state" class="form-control" value="{{ old('state', $guest->state) }}" placeholder="Optional">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="postal_code" class="form-label">Postal Code</label>
                        <input type="text" name="postal_code" id="postal_code" class="form-control" value="{{ old('postal_code', $guest->postal_code) }}" placeholder="Optional">
                    </div>
                    <div class="form-group">
                        <label for="id_type" class="form-label">Identification Type</label>
                        <select name="id_type" id="id_type" class="form-control form-select" required>
                            <option value="">Select ID type</option>
                            @foreach(['passport' => 'Passport', 'national_id' => 'National ID', 'drivers_license' => 'Driver License', 'residence_permit' => 'Residence Permit', 'voter_card' => 'Voter Card', 'other' => 'Other Government ID'] as $value => $label)
                                <option value="{{ $value }}" {{ old('id_type', $guest->id_type) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="id_number" class="form-label">Identification Number</label>
                    <input type="text" name="id_number" id="id_number" class="form-control" value="{{ old('id_number', $guest->id_number) }}" placeholder="Passport, national ID, or other accepted ID number" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="emergency_contact_name" class="form-label">Emergency Contact Name</label>
                        <input type="text" name="emergency_contact_name" id="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name', $guest->emergency_contact_name) }}" placeholder="Full name" required>
                    </div>
                    <div class="form-group">
                        <label for="emergency_contact_relationship" class="form-label">Relationship</label>
                        <input type="text" name="emergency_contact_relationship" id="emergency_contact_relationship" class="form-control" value="{{ old('emergency_contact_relationship', $guest->emergency_contact_relationship) }}" placeholder="e.g. Spouse, Parent, Friend" required>
                    </div>
                </div>

                @include('partials.phone-input', ['field' => 'emergency_contact_phone', 'label' => 'Emergency Contact Phone', 'value' => old('emergency_contact_phone', $guest->emergency_contact_phone), 'required' => true])

                <label style="display:flex;align-items:flex-start;gap:0.65rem;margin-top:0.5rem;color:var(--text-secondary);font-size:0.86rem;line-height:1.5;">
                    <input type="checkbox" name="marketing_consent" value="1" {{ old('marketing_consent', $guest->marketing_consent) ? 'checked' : '' }} style="margin-top:0.2rem;">
                    <span>Send me stay updates, direct-booking offers, and hotel news. I can opt out later.</span>
                </label>
            </div>

            <div class="form-group" style="margin-top: 1.25rem;">
                <label for="coupon_code" class="form-label">Promo Coupon Code</label>
                <div style="display: flex; gap: 0.5rem;">
                    <input type="text" name="coupon_code" id="coupon_code" class="form-control" placeholder="e.g. SAVE10" style="text-transform: uppercase;">
                    <button type="button" id="apply-coupon-btn" class="btn btn-outline" style="white-space: nowrap; padding: 0.5rem 1rem;">Apply</button>
                </div>
                <span id="coupon-message" style="display: block; margin-top: 0.25rem; font-size: 0.8rem; font-weight: 500;"></span>
            </div>

            @if(isset($coupons) && $coupons->count() > 0)
            <div class="form-group" style="margin-top: 0.75rem;">
                <label class="form-label" style="font-size: 0.85rem; color: var(--text-secondary);">Available Coupons (Click to apply):</label>
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.25rem;">
                    @foreach($coupons as $coupon)
                        <button type="button" class="coupon-badge" data-code="{{ $coupon->code }}" style="background: rgba(110, 68, 255, 0.1); border: 1px solid rgba(110, 68, 255, 0.25); color: #855eff; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                            {{ $coupon->code }} ({{ (float)$coupon->discount_percentage }}% Off)
                        </button>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="form-group" style="margin-top: 1.5rem;">
                <label class="form-label" style="font-weight: 600;"><i class="fa-solid fa-circle-plus" style="color: var(--primary);"></i> Enhance Your Stay (Add-ons)</label>
                <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-top: 0.5rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.9rem;">
                        <input type="checkbox" name="addons[]" value="airport_shuttle" class="addon-checkbox" data-price="30" data-type="one_time">
                        <span>Airport Shuttle - <strong>$30.00</strong> <span style="font-size: 0.8rem; color: var(--text-secondary);">(One-time)</span></span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.9rem;">
                        <input type="checkbox" name="addons[]" value="luxury_spa" class="addon-checkbox" data-price="50" data-type="one_time">
                        <span>Luxury Spa Treatment - <strong>$50.00</strong> <span style="font-size: 0.8rem; color: var(--text-secondary);">(One-time)</span></span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.9rem;">
                        <input type="checkbox" name="addons[]" value="gourmet_breakfast" class="addon-checkbox" data-price="20" data-type="per_night">
                        <span>Gourmet Breakfast - <strong>$20.00</strong> <span style="font-size: 0.8rem; color: var(--text-secondary);">(Per night)</span></span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.9rem;">
                        <input type="checkbox" name="addons[]" value="premium_minibar" class="addon-checkbox" data-price="40" data-type="one_time">
                        <span>Premium Mini-Bar - <strong>$40.00</strong> <span style="font-size: 0.8rem; color: var(--text-secondary);">(One-time)</span></span>
                    </label>
                </div>
            </div>

            @if(auth()->user()->loyalty_points > 0)
            <div class="form-group" style="margin-top: 1.5rem; padding: 0.75rem; background: rgba(110, 68, 255, 0.05); border-radius: 8px; border: 1px solid rgba(110, 68, 255, 0.15);">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.9rem; margin-bottom: 0;">
                    <input type="checkbox" name="redeem_loyalty" id="redeem_loyalty" value="1" style="width: 1.1rem; height: 1.1rem;">
                    <span>
                        Redeem Loyalty Points<br>
                        <small style="color: var(--text-secondary);">Balance: <strong>{{ auth()->user()->loyalty_points }} points</strong> (Worth <strong>${{ number_format(auth()->user()->loyalty_points * 0.10, 2) }}</strong> discount)</small>
                    </span>
                </label>
            </div>
            @endif
            
            <div style="margin-top: 2rem; border-top: 1px dashed var(--border-color); padding-top: 1.5rem;">
                <button type="submit" class="btn btn-primary btn-block">
                    Confirm & Proceed to Payment
                </button>
            </div>
        </form>
    </div>
    
    <!-- Right Column: Room Details & Real-time Bill Breakdown -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="glass-panel">
            <h3 style="font-size: 1.15rem; margin-bottom: 1rem; color: var(--primary);">{{ $roomType->name }}</h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 1.25rem;">
                {{ $roomType->description }}
            </p>
            
            <div style="display: flex; flex-wrap: wrap; gap: 0.4rem; margin-bottom: 1.5rem;">
                @foreach($roomType->amenities as $amenity)
                    <span class="room-amenity-tag" style="font-size: 0.75rem;">{{ $amenity }}</span>
                @endforeach
            </div>
            
            <div style="border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                <h4 style="font-size: 1rem; margin-bottom: 1rem;"><i class="fa-solid fa-receipt" style="color: var(--primary);"></i> Price Breakdown</h4>
                
                <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.9rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-secondary);">Room Rate (per night)</span>
                        <span style="font-weight: 600;">${{ number_format($roomType->base_price, 2) }}</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-secondary);" id="nights-label">Duration (Nights)</span>
                        <span style="font-weight: 600;" id="nights-count">1 night</span>
                    </div>

                    <div style="display: none; justify-content: space-between; color: var(--success);" id="coupon-row">
                        <span id="coupon-label">Promo Discount (0%)</span>
                        <span style="font-weight: 600;" id="discount-amount">-$0.00</span>
                    </div>

                    <div style="display: none; justify-content: space-between; color: var(--success);" id="loyalty-row">
                        <span>Loyalty Discount</span>
                        <span style="font-weight: 600;" id="loyalty-amount">-$0.00</span>
                    </div>

                    <div style="display: none; justify-content: space-between; color: var(--text-secondary);" id="addons-row">
                        <span>Selected Add-ons</span>
                        <span style="font-weight: 600;" id="addons-amount">$0.00</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
                        <span style="color: var(--text-secondary);">Taxes & Fees ({{ $taxRate }}%)</span>
                        <span style="font-weight: 600;" id="tax-amount">$0.00</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: 800; padding-top: 0.25rem;">
                        <span>Estimated Total</span>
                        <span style="color: var(--primary);" id="total-price">$0.00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const checkInDate = document.getElementById('check_in_date');
    const checkOutDate = document.getElementById('check_out_date');
    const nightsCount = document.getElementById('nights-count');
    const nightsLabel = document.getElementById('nights-label');
    const taxAmount = document.getElementById('tax-amount');
    const totalPrice = document.getElementById('total-price');

    const couponInput = document.getElementById('coupon_code');
    const applyCouponBtn = document.getElementById('apply-coupon-btn');
    const couponMessage = document.getElementById('coupon-message');
    const couponRow = document.getElementById('coupon-row');
    const couponLabel = document.getElementById('coupon-label');
    const discountAmount = document.getElementById('discount-amount');

    const addonsRow = document.getElementById('addons-row');
    const addonsAmount = document.getElementById('addons-amount');
    const loyaltyRow = document.getElementById('loyalty-row');
    const loyaltyAmount = document.getElementById('loyalty-amount');
    const redeemLoyaltyCheckbox = document.getElementById('redeem_loyalty');
    const addonCheckboxes = document.querySelectorAll('.addon-checkbox');
    
    const basePrice = {{ $roomType->base_price }};
    const taxRate = {{ $taxRate }} / 100;
    const userLoyaltyPoints = {{ auth()->user()->loyalty_points ?? 0 }};
    const loyaltyPointValue = 0.10;

    let activeDiscountPercentage = 0;
    
    function calculateBill() {
        const checkIn = new Date(checkInDate.value);
        const checkOut = new Date(checkOutDate.value);
        
        if (checkIn && checkOut && checkOut > checkIn) {
            const timeDiff = checkOut.getTime() - checkIn.getTime();
            const nights = Math.ceil(timeDiff / (1000 * 3600 * 24));
            
            const subtotal = basePrice * nights;
            const discount = subtotal * (activeDiscountPercentage / 100);
            const discountedSubtotal = subtotal - discount;

            // Loyalty Points discount
            let loyaltyDiscount = 0;
            if (redeemLoyaltyCheckbox && redeemLoyaltyCheckbox.checked) {
                const maxLoyaltyValue = userLoyaltyPoints * loyaltyPointValue;
                if (maxLoyaltyValue >= discountedSubtotal) {
                    loyaltyDiscount = discountedSubtotal;
                } else {
                    loyaltyDiscount = maxLoyaltyValue;
                }
            }

            const subtotalAfterLoyalty = discountedSubtotal - loyaltyDiscount;

            // Add-ons cost calculation
            let addonsCost = 0;
            addonCheckboxes.forEach(cb => {
                if (cb.checked) {
                    const price = parseFloat(cb.getAttribute('data-price'));
                    const type = cb.getAttribute('data-type');
                    if (type === 'per_night') {
                        addonsCost += price * nights;
                    } else {
                        addonsCost += price;
                    }
                }
            });

            const finalSubtotal = subtotalAfterLoyalty + addonsCost;
            const tax = finalSubtotal * taxRate;
            const total = finalSubtotal + tax;
            
            nightsCount.innerText = `${nights} night${nights > 1 ? 's' : ''}`;
            nightsLabel.innerText = `Duration (${nights} Night${nights > 1 ? 's' : ''})`;

            if (activeDiscountPercentage > 0) {
                couponRow.style.display = 'flex';
                couponLabel.innerText = `Promo Discount (${activeDiscountPercentage}%)`;
                discountAmount.innerText = `-$${discount.toFixed(2)}`;
            } else {
                couponRow.style.display = 'none';
            }

            if (loyaltyDiscount > 0) {
                loyaltyRow.style.display = 'flex';
                loyaltyAmount.innerText = `-$${loyaltyDiscount.toFixed(2)}`;
            } else {
                loyaltyRow.style.display = 'none';
            }

            if (addonsCost > 0) {
                addonsRow.style.display = 'flex';
                addonsAmount.innerText = `$${addonsCost.toFixed(2)}`;
            } else {
                addonsRow.style.display = 'none';
            }

            taxAmount.innerText = `$${tax.toFixed(2)}`;
            totalPrice.innerText = `$${total.toFixed(2)}`;
        } else {
            nightsCount.innerText = '0 nights';
            couponRow.style.display = 'none';
            loyaltyRow.style.display = 'none';
            addonsRow.style.display = 'none';
            taxAmount.innerText = '$0.00';
            totalPrice.innerText = '$0.00';
        }
    }
    
    checkInDate.addEventListener('change', function() {
        const cIn = new Date(this.value);
        cIn.setDate(cIn.getDate() + 1);
        const yyyy = cIn.getFullYear();
        const mm = String(cIn.getMonth() + 1).padStart(2, '0');
        const dd = String(cIn.getDate()).padStart(2, '0');
        
        checkOutDate.min = `${yyyy}-${mm}-${dd}`;
        if (checkOutDate.value <= this.value) {
            checkOutDate.value = `${yyyy}-${mm}-${dd}`;
        }
        calculateBill();
    });
    
    checkOutDate.addEventListener('change', calculateBill);

    if (redeemLoyaltyCheckbox) {
        redeemLoyaltyCheckbox.addEventListener('change', calculateBill);
    }

    addonCheckboxes.forEach(cb => {
        cb.addEventListener('change', calculateBill);
    });

    applyCouponBtn.addEventListener('click', function() {
        const code = couponInput.value.trim();
        if (!code) {
            couponMessage.style.color = 'var(--danger)';
            couponMessage.innerText = 'Please enter a coupon code.';
            return;
        }

        couponMessage.style.color = 'var(--text-secondary)';
        couponMessage.innerText = 'Verifying...';

        fetch('{{ route('customer.coupons.verify') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ code: code })
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(res => {
            if (res.status === 200) {
                activeDiscountPercentage = res.body.discount_percentage;
                couponMessage.style.color = 'var(--success)';
                couponMessage.innerText = res.body.message;
                calculateBill();
            } else {
                activeDiscountPercentage = 0;
                couponMessage.style.color = 'var(--danger)';
                couponMessage.innerText = res.body.message || 'Invalid coupon code.';
                calculateBill();
            }
        })
        .catch(err => {
            activeDiscountPercentage = 0;
            couponMessage.style.color = 'var(--danger)';
            couponMessage.innerText = 'Error verifying coupon.';
            calculateBill();
        });
    });

    // Coupon badges click handler
    document.querySelectorAll('.coupon-badge').forEach(badge => {
        badge.addEventListener('click', function() {
            couponInput.value = this.getAttribute('data-code');
            applyCouponBtn.click();
        });
    });
    
    // Initial call
    calculateBill();
</script>
@endsection
