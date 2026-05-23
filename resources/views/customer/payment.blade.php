@extends('layouts.app')

@section('title', 'Secure Checkout')

@section('content')
@php
    $gateway = \App\Models\Setting::getValue('active_payment_gateway', 'card_simulation');
@endphp

<style>
    /* Premium style overrides for checkout */
    .payment-summary-card {
        background: var(--card-bg, rgba(255, 255, 255, 0.7));
        backdrop-filter: blur(12px);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 2.5rem;
        width: 100%;
        max-width: 500px;
        box-shadow: var(--shadow-lg);
        text-align: center;
    }
    
    .gateway-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
    }
    
    .gateway-badge.paystack {
        background: rgba(55, 214, 122, 0.15);
        color: #37d67a;
    }
    
    .gateway-badge.flutterwave {
        background: rgba(245, 166, 35, 0.15);
        color: #f5a623;
    }

    .gateway-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(6px);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10000;
        opacity: 0;
        pointer-events: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .gateway-overlay.active {
        opacity: 1;
        pointer-events: auto;
    }
    
    .paystack-modal {
        width: 90%;
        max-width: 620px;
        height: 480px;
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        display: flex;
        overflow: hidden;
        color: #1e293b;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        transform: scale(0.95);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    
    .gateway-overlay.active .paystack-modal {
        transform: scale(1);
    }
    
    .paystack-sidebar {
        width: 200px;
        background: #f8fafc;
        border-right: 1px solid #e2e8f0;
        padding: 1.75rem 1.25rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    
    .paystack-methods {
        list-style: none;
        padding: 0;
        margin: 2rem 0;
    }
    
    .paystack-method-item {
        padding: 0.65rem 0.5rem;
        font-size: 0.85rem;
        font-weight: 500;
        color: #64748b;
        cursor: pointer;
        border-radius: 4px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }
    
    .paystack-method-item.active {
        background: #f1f5f9;
        color: #09a5db;
        font-weight: 600;
    }
    
    .paystack-main {
        flex: 1;
        padding: 2.25rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        background: #ffffff;
    }
    
    .paystack-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 1rem;
    }
    
    .paystack-amount {
        font-size: 1.3rem;
        font-weight: 700;
        color: #0f172a;
    }
    
    .paystack-form-body {
        margin-top: 1.5rem;
        flex-grow: 1;
    }
    
    .paystack-input {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 0.65rem;
        font-size: 0.95rem;
        outline: none;
        background: #ffffff;
        color: #0f172a;
        transition: border-color 0.2s;
    }
    
    .paystack-input:focus {
        border-color: #37d67a;
        box-shadow: 0 0 0 3px rgba(55, 214, 122, 0.15);
    }
    
    .paystack-button {
        background: #37d67a;
        color: #ffffff;
        border: none;
        border-radius: 4px;
        padding: 0.75rem;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        width: 100%;
        margin-top: 1.5rem;
        transition: all 0.2s;
    }
    
    .paystack-button:hover {
        background: #2cb565;
    }
    
    .flutterwave-modal {
        width: 90%;
        max-width: 400px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        color: #1e293b;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        transform: scale(0.95);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    
    .gateway-overlay.active .flutterwave-modal {
        transform: scale(1);
    }
    
    .flutterwave-header {
        background: #f8fafc;
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .flutterwave-body {
        padding: 1.75rem;
        background: #ffffff;
    }
    
    .flutterwave-input {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 0.65rem;
        font-size: 0.95rem;
        outline: none;
        background: #ffffff;
        color: #0f172a;
        transition: border-color 0.2s;
    }
    
    .flutterwave-input:focus {
        border-color: #f5a623;
        box-shadow: 0 0 0 3px rgba(245, 166, 35, 0.15);
    }
    
    .flutterwave-button {
        background: #f5a623;
        color: #ffffff;
        border: none;
        border-radius: 6px;
        padding: 0.75rem;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        width: 100%;
        margin-top: 1.5rem;
        transition: all 0.2s;
    }
    
    .flutterwave-button:hover {
        background: #e09212;
    }
    
    .flutterwave-footer {
        padding: 1rem;
        text-align: center;
        font-size: 0.75rem;
        color: #64748b;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
    }
    
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.95);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s ease;
    }
    
    .loading-overlay.active {
        opacity: 1;
        pointer-events: auto;
    }
    
    .spinner {
        width: 45px;
        height: 45px;
        border: 4px solid #f1f5f9;
        border-top: 4px solid var(--spinner-color, #37d67a);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-bottom: 1rem;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .otp-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 1100;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s ease;
        padding: 2rem;
        text-align: center;
        color: #1e293b;
    }
    
    .otp-overlay.active {
        opacity: 1;
        pointer-events: auto;
    }
</style>

@if($gateway === 'paystack')
    <!-- Paystack Order Summary Page -->
    <div class="payment-wrapper animate-fade-in" style="display: flex; justify-content: center; align-items: center; flex-direction: column; min-height: 60vh; width: 100%;">
        <div class="payment-summary-card">
            <div class="gateway-badge paystack">
                <i class="fa-solid fa-credit-card"></i> Paystack Gateway Active
            </div>
            
            <p style="color: var(--text-secondary); font-size: 0.95rem; font-weight: 500;">Reservations Total Bill</p>
            <h2 style="font-size: 2.5rem; color: var(--primary); font-family: 'Outfit', sans-serif; margin: 0.5rem 0;">
                ${{ number_format($booking->total_price, 2) }}
            </h2>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 2rem;">
                Room #{{ $booking->room->room_number }} • {{ $booking->room->roomType->name }}
            </p>
            
            <button class="btn btn-primary btn-block" onclick="openGatewayModal()" style="background: #37d67a; border-color: #37d67a;">
                <i class="fa-solid fa-lock"></i> Pay via Paystack Popup
            </button>
            
            <a href="{{ route('customer.bookings') }}" class="btn btn-outline btn-block" style="margin-top: 0.75rem;">
                Cancel & Return
            </a>
        </div>
    </div>
    
    <!-- Paystack Checkout Overlay Widget -->
    <div class="gateway-overlay" id="gatewayOverlay">
        <div class="paystack-modal">
            <!-- Loading Overlay inside Paystack Modal -->
            <div class="loading-overlay" id="gatewayLoader" style="--spinner-color: #37d67a;">
                <div class="spinner"></div>
                <h4 style="font-weight: 600; color: #1e293b;" id="loaderText">Authorizing Payment...</h4>
                <p style="font-size: 0.8rem; color: #64748b; margin-top: 0.25rem;">Please do not close this window.</p>
            </div>
            
            <!-- OTP Challenge Overlay inside Paystack Modal -->
            <div class="otp-overlay" id="otpOverlay">
                <h3 style="font-weight: 700; margin-bottom: 0.75rem;"><i class="fa-solid fa-shield-halved" style="color: #37d67a;"></i> Verification Required</h3>
                <p style="font-size: 0.9rem; color: #64748b; margin-bottom: 1.5rem;">We sent a one-time passcode (OTP) to your phone number registered with this card.</p>
                <div style="margin-bottom: 1.5rem; width: 100%; max-width: 200px; margin-left: auto; margin-right: auto;">
                    <input type="text" id="paystack_otp" class="paystack-input" style="text-align: center; font-size: 1.5rem; letter-spacing: 0.5rem;" maxlength="6" placeholder="123456">
                </div>
                <button type="button" class="paystack-button" onclick="submitOtp()" style="margin-top: 0;">Verify & Complete</button>
                <button type="button" class="btn btn-outline" onclick="closeGatewayModal()" style="border: none; margin-top: 0.5rem; font-size: 0.85rem; color: #64748b;">Cancel</button>
            </div>

            <!-- Paystack sidebar -->
            <div class="paystack-sidebar">
                <div>
                    <div class="paystack-logo">pay<span>stack</span></div>
                    <ul class="paystack-methods">
                        <li class="paystack-method-item active"><i class="fa-solid fa-credit-card"></i> Card</li>
                        <li class="paystack-method-item"><i class="fa-solid fa-building-columns"></i> Bank</li>
                        <li class="paystack-method-item"><i class="fa-solid fa-mobile-screen-button"></i> Transfer</li>
                    </ul>
                </div>
                <div style="font-size: 0.7rem; color: #94a3b8; display: flex; align-items: center; gap: 0.25rem;">
                    <i class="fa-solid fa-lock"></i> SECURED BY PAYSTACK
                </div>
            </div>
            
            <!-- Paystack main form panel -->
            <div class="paystack-main">
                <div class="paystack-header">
                    <span style="font-size: 0.8rem; color: #64748b;">{{ auth()->user()->email }}</span>
                    <span class="paystack-amount">${{ number_format($booking->total_price, 2) }}</span>
                </div>
                
                <form action="{{ route('customer.payment.process', $booking->id) }}" method="POST" id="gateway-payment-form">
                    @csrf
                    <div class="paystack-form-body">
                        <h4 style="font-size: 0.95rem; font-weight: 600; margin-bottom: 1rem; color: #475569;">Enter Card Details</h4>
                        
                        <div style="margin-bottom: 1rem;">
                            <label class="form-label" style="font-size: 0.75rem; color: #64748b;">Card Number</label>
                            <input type="text" name="card_number" id="gateway_card_number" class="paystack-input" placeholder="4111 2222 3333 4444" maxlength="19" required>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 1rem;">
                            <div>
                                <label class="form-label" style="font-size: 0.75rem; color: #64748b;">Expiry Date</label>
                                <input type="text" name="card_expiry" id="gateway_card_expiry" class="paystack-input" placeholder="MM/YY" maxlength="5" required>
                            </div>
                            <div>
                                <label class="form-label" style="font-size: 0.75rem; color: #64748b;">CVV</label>
                                <input type="text" name="card_cvv" id="gateway_card_cvv" class="paystack-input" placeholder="123" maxlength="3" required>
                            </div>
                        </div>
                        
                        <input type="hidden" name="card_name" id="gateway_card_name" value="{{ auth()->user()->name }}">
                    </div>
                    
                    <button type="submit" class="paystack-button" id="paystackPayBtn">
                        Pay ${{ number_format($booking->total_price, 2) }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@elseif($gateway === 'flutterwave')
    <!-- Flutterwave Order Summary Page -->
    <div class="payment-wrapper animate-fade-in" style="display: flex; justify-content: center; align-items: center; flex-direction: column; min-height: 60vh; width: 100%;">
        <div class="payment-summary-card">
            <div class="gateway-badge flutterwave">
                <i class="fa-solid fa-credit-card"></i> Flutterwave Gateway Active
            </div>
            
            <p style="color: var(--text-secondary); font-size: 0.95rem; font-weight: 500;">Reservations Total Bill</p>
            <h2 style="font-size: 2.5rem; color: var(--primary); font-family: 'Outfit', sans-serif; margin: 0.5rem 0;">
                ${{ number_format($booking->total_price, 2) }}
            </h2>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 2rem;">
                Room #{{ $booking->room->room_number }} • {{ $booking->room->roomType->name }}
            </p>
            
            <button class="btn btn-primary btn-block" onclick="openGatewayModal()" style="background: #f5a623; border-color: #f5a623;">
                <i class="fa-solid fa-lock"></i> Pay via Flutterwave Modal
            </button>
            
            <a href="{{ route('customer.bookings') }}" class="btn btn-outline btn-block" style="margin-top: 0.75rem;">
                Cancel & Return
            </a>
        </div>
    </div>
    
    <!-- Flutterwave Checkout Overlay Widget -->
    <div class="gateway-overlay" id="gatewayOverlay">
        <div class="flutterwave-modal">
            <!-- Loading Overlay inside Flutterwave Modal -->
            <div class="loading-overlay" id="gatewayLoader" style="--spinner-color: #f5a623;">
                <div class="spinner"></div>
                <h4 style="font-weight: 600; color: #1e293b;" id="loaderText">Processing transaction...</h4>
                <p style="font-size: 0.8rem; color: #64748b; margin-top: 0.25rem;">Do not refresh this page.</p>
            </div>
            
            <!-- OTP Challenge Overlay inside Flutterwave Modal -->
            <div class="otp-overlay" id="otpOverlay">
                <h3 style="font-weight: 700; margin-bottom: 0.75rem;"><i class="fa-solid fa-shield-halved" style="color: #f5a623;"></i> Card Verification</h3>
                <p style="font-size: 0.9rem; color: #64748b; margin-bottom: 1.5rem;">Enter the verification code sent to your mobile phone to complete payment.</p>
                <div style="margin-bottom: 1.5rem; width: 100%; max-width: 200px; margin-left: auto; margin-right: auto;">
                    <input type="text" id="flutterwave_otp" class="flutterwave-input" style="text-align: center; font-size: 1.5rem; letter-spacing: 0.5rem;" maxlength="6" placeholder="123456">
                </div>
                <button type="button" class="flutterwave-button" onclick="submitOtp()" style="margin-top: 0;">Validate OTP</button>
                <button type="button" class="btn btn-outline" onclick="closeGatewayModal()" style="border: none; margin-top: 0.5rem; font-size: 0.85rem; color: #64748b;">Cancel</button>
            </div>

            <!-- Flutterwave header -->
            <div class="flutterwave-header">
                <div class="flutterwave-logo"><i class="fa-solid fa-feather-pointed"></i> flutterwave</div>
                <div class="flutterwave-amount-box">
                    <span style="font-size: 0.75rem; color: #64748b; display: block;">PAY</span>
                    <span class="flutterwave-amount">${{ number_format($booking->total_price, 2) }}</span>
                </div>
            </div>
            
            <!-- Flutterwave body -->
            <div class="flutterwave-body">
                <form action="{{ route('customer.payment.process', $booking->id) }}" method="POST" id="gateway-payment-form">
                    @csrf
                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" style="font-size: 0.75rem; color: #64748b; font-weight: 600;">CARD NUMBER</label>
                        <input type="text" name="card_number" id="gateway_card_number" class="flutterwave-input" placeholder="4111 2222 3333 4444" maxlength="19" required>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 1.25rem;">
                        <div>
                            <label class="form-label" style="font-size: 0.75rem; color: #64748b; font-weight: 600;">VALID UNTIL</label>
                            <input type="text" name="card_expiry" id="gateway_card_expiry" class="flutterwave-input" placeholder="MM/YY" maxlength="5" required>
                        </div>
                        <div>
                            <label class="form-label" style="font-size: 0.75rem; color: #64748b; font-weight: 600;">CVV</label>
                            <input type="text" name="card_cvv" id="gateway_card_cvv" class="flutterwave-input" placeholder="123" maxlength="3" required>
                        </div>
                    </div>
                    
                    <input type="hidden" name="card_name" id="gateway_card_name" value="{{ auth()->user()->name }}">
                    
                    <button type="submit" class="flutterwave-button" id="flutterwavePayBtn">
                        Pay ${{ number_format($booking->total_price, 2) }}
                    </button>
                </form>
            </div>
            
            <div class="flutterwave-footer">
                <i class="fa-solid fa-lock"></i> Secured by Flutterwave
            </div>
        </div>
    </div>
@else
    <!-- Standard Credit Card Simulation -->
    <div class="payment-wrapper animate-fade-in" style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%;">
        <!-- Booking Total Header -->
        <div style="text-align: center; margin-bottom: 1rem;">
            <p style="color: var(--text-secondary); font-size: 0.95rem; font-weight: 500;">Reservations Total Bill</p>
            <h2 style="font-size: 2.5rem; color: var(--primary); font-family: 'Outfit', sans-serif;">
                ${{ number_format($booking->total_price, 2) }}
            </h2>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">
                Room #{{ $booking->room->room_number }} • {{ $booking->room->roomType->name }}
            </p>
        </div>

        <!-- 3D Credit Card Widget -->
        <div class="card-container" onclick="toggleCardFlip()" style="margin-bottom: 2rem;">
            <div class="credit-card" id="credit-card">
                <!-- Front of Card -->
                <div class="credit-card-front">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="card-chip"></div>
                        <div class="card-brand"><i class="fa-brands fa-cc-visa" style="font-size: 2.2rem;"></i></div>
                    </div>
                    
                    <div class="card-number" id="card-number-display">•••• •••• •••• ••••</div>
                    
                    <div class="card-footer">
                        <div class="card-holder">
                            <span class="card-label">Card Holder</span>
                            <span class="card-value" id="card-name-display">Your Name</span>
                        </div>
                        <div class="card-expires">
                            <span class="card-label">Expires</span>
                            <span class="card-value" id="card-expiry-display">MM/YY</span>
                        </div>
                    </div>
                </div>
                
                <!-- Back of Card -->
                <div class="credit-card-back">
                    <div class="card-black-stripe"></div>
                    <div class="card-cvv-stripe">
                        <span class="card-label" style="color: #fff; margin-bottom: 0.25rem;">Security Code (CVV)</span>
                        <div class="card-cvv-box" id="card-cvv-display">•••</div>
                    </div>
                    <div style="padding: 0 1.5rem; text-align: right; opacity: 0.6; font-size: 0.65rem;">
                        AETHERIA HMS MOCK GATEWAY SYSTEM
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Inputs Form -->
        <div class="glass-panel" style="width: 100%; max-width: 480px; padding: 2rem;">
            <form action="{{ route('customer.payment.process', $booking->id) }}" method="POST" id="payment-form">
                @csrf
                
                <div class="form-group">
                    <label for="card_number" class="form-label">Card Number</label>
                    <input type="text" name="card_number" id="card_number" class="form-control" placeholder="4111 2222 3333 4444" maxlength="19" required>
                </div>
                
                <div class="form-group">
                    <label for="card_name" class="form-label">Cardholder Name</label>
                    <input type="text" name="card_name" id="card_name" class="form-control" placeholder="John Watson" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="card_expiry" class="form-label">Expiration Date</label>
                        <input type="text" name="card_expiry" id="card_expiry" class="form-control" placeholder="MM/YY" maxlength="5" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="card_cvv" class="form-label">CVV / CVC</label>
                        <input type="text" name="card_cvv" id="card_cvv" class="form-control" placeholder="123" maxlength="4" required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1.5rem;">
                    <i class="fa-solid fa-lock"></i> Process Payment
                </button>
            </form>
        </div>
    </div>
@endif
@endsection

@section('scripts')
@if($gateway === 'paystack' || $gateway === 'flutterwave')
<script>
    const gatewayOverlay = document.getElementById('gatewayOverlay');
    const gatewayLoader = document.getElementById('gatewayLoader');
    const otpOverlay = document.getElementById('otpOverlay');
    const loaderText = document.getElementById('loaderText');
    const gatewayForm = document.getElementById('gateway-payment-form');
    
    const cardInput = document.getElementById('gateway_card_number');
    const expiryInput = document.getElementById('gateway_card_expiry');
    const cvvInput = document.getElementById('gateway_card_cvv');

    function openGatewayModal() {
        gatewayOverlay.classList.add('active');
        gatewayLoader.classList.remove('active');
        otpOverlay.classList.remove('active');
    }
    
    function closeGatewayModal() {
        gatewayOverlay.classList.remove('active');
    }
    
    // Auto-formatting card inputs
    if (cardInput) {
        cardInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = '';
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) {
                    formattedValue += ' ';
                }
                formattedValue += value[i];
            }
            e.target.value = formattedValue;
        });
    }
    
    if (expiryInput) {
        expiryInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            if (value.length > 2) {
                value = value.substr(0, 2) + '/' + value.substr(2, 2);
            }
            e.target.value = value;
        });
    }

    if (cvvInput) {
        cvvInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
        });
    }
    
    gatewayForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show Loading Overlay
        gatewayLoader.classList.add('active');
        loaderText.innerText = 'Connecting to Bank Gateway...';
        
        // Simulate bank authorization response delay, then show OTP verification
        setTimeout(() => {
            gatewayLoader.classList.remove('active');
            otpOverlay.classList.add('active');
        }, 1500);
    });
    
    function submitOtp() {
        const otpVal = document.getElementById('{{ $gateway }}_otp').value;
        if (otpVal.length < 4) {
            alert('Please enter a valid verification code.');
            return;
        }
        
        // Show loading spinner during submission approval simulation
        otpOverlay.classList.remove('active');
        gatewayLoader.classList.add('active');
        loaderText.innerText = 'Transaction Approved! Confirming...';
        
        // Submit the form to the backend controller after a brief confirmation delay
        setTimeout(() => {
            gatewayForm.submit();
        }, 1200);
    }
</script>
@else
<script>
    const creditCard = document.getElementById('credit-card');
    
    const cardNumberInput = document.getElementById('card_number');
    const cardNameInput = document.getElementById('card_name');
    const cardExpiryInput = document.getElementById('card_expiry');
    const cardCvvInput = document.getElementById('card_cvv');
    
    const cardNumberDisplay = document.getElementById('card-number-display');
    const cardNameDisplay = document.getElementById('card-name-display');
    const cardExpiryDisplay = document.getElementById('card-expiry-display');
    const cardCvvDisplay = document.getElementById('card-cvv-display');
    
    function flipCardToBack() {
        creditCard.classList.add('flipped');
    }
    function flipCardToFront() {
        creditCard.classList.remove('flipped');
    }
    function toggleCardFlip() {
        creditCard.classList.toggle('flipped');
    }
    
    cardNumberInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
        let formattedValue = '';
        for (let i = 0; i < value.length; i++) {
            if (i > 0 && i % 4 === 0) {
                formattedValue += ' ';
            }
            formattedValue += value[i];
        }
        e.target.value = formattedValue;
        
        cardNumberDisplay.innerText = formattedValue || '•••• •••• •••• ••••';
    });
    
    cardNameInput.addEventListener('input', function(e) {
        cardNameDisplay.innerText = e.target.value.toUpperCase() || 'YOUR NAME';
    });
    
    cardExpiryInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
        if (value.length > 2) {
            value = value.substr(0, 2) + '/' + value.substr(2, 2);
        }
        e.target.value = value;
        cardExpiryDisplay.innerText = value || 'MM/YY';
    });
    
    cardCvvInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
        e.target.value = value;
        cardCvvDisplay.innerText = value || '•••';
    });
    
    cardCvvInput.addEventListener('focus', flipCardToBack);
    cardCvvInput.addEventListener('blur', flipCardToFront);
</script>
@endif
@endsection
