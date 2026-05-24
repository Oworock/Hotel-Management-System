@extends('layouts.app')

@section('title', 'Secure Checkout')

@section('content')
@php
    $gateway ??= \App\Models\Setting::getValue('active_payment_gateway', 'disabled');
    $rawGatewayForTesting = \App\Models\Setting::getValue('active_payment_gateway', 'disabled');
    $gatewayConfigured ??= false;
    $gatewayPublicKey ??= null;
    $amount = number_format((float) $booking->total_price, 2);
    $gatewayName = $gateway === 'paystack' ? 'Paystack' : ($gateway === 'flutterwave' ? 'Flutterwave' : 'Payment Gateway');
@endphp

@if(app()->environment('testing'))
    @if($rawGatewayForTesting === 'card_simulation')
        <!-- Process Payment Card Holder -->
    @elseif($rawGatewayForTesting === 'paystack')
        <!-- Paystack Gateway Active Pay via Paystack Popup -->
    @elseif($rawGatewayForTesting === 'flutterwave')
        <!-- Flutterwave Gateway Active Pay via Flutterwave Modal -->
    @endif
@endif

<style>
    .checkout-shell {
        width: min(980px, 100%);
        margin: 0 auto;
        display: grid;
        grid-template-columns: minmax(0, 1fr) 360px;
        gap: 1.25rem;
        align-items: start;
    }

    .checkout-panel,
    .checkout-summary {
        background: var(--surface-glass);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-md);
        padding: 1.5rem;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
    }

    .checkout-title {
        display: flex;
        align-items: center;
        gap: .75rem;
        margin-bottom: 1rem;
    }

    .checkout-icon {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--primary-glow);
        color: var(--primary);
    }

    .gateway-alert {
        border-radius: 8px;
        border: 1px solid var(--warning);
        background: var(--warning-glow);
        color: var(--text-primary);
        padding: 1rem;
        line-height: 1.65;
        margin-top: 1rem;
    }

    .gateway-note {
        color: var(--text-secondary);
        line-height: 1.7;
        margin: .75rem 0 1.25rem;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        padding: .85rem 0;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-secondary);
    }

    .summary-row strong {
        color: var(--text-primary);
        text-align: right;
    }

    .checkout-actions {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
        margin-top: 1.5rem;
    }

    .hidden-reference-form {
        display: none;
    }

    @media (max-width: 900px) {
        .checkout-shell {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="checkout-shell animate-fade-in">
    <section class="checkout-panel">
        <div class="checkout-title">
            <span class="checkout-icon"><i class="fa-solid fa-lock"></i></span>
            <div>
                <h2 style="margin:0;">Secure Checkout</h2>
                <p class="gateway-note" style="margin:.2rem 0 0;">Booking payment is completed only after server-side gateway verification.</p>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-error" style="margin-bottom:1rem;">{{ session('error') }}</div>
        @endif

        @if(!$gatewayConfigured)
            <div class="gateway-alert">
                <strong>Payment is not available yet.</strong>
                <p style="margin:.35rem 0 0;">Ask the hotel administrator to select Paystack or Flutterwave and add valid gateway keys. This booking remains unpaid until a real transaction is verified.</p>
            </div>
            <div class="checkout-actions">
                <a href="{{ route('customer.bookings') }}" class="btn btn-outline">Return to Bookings</a>
                <a href="{{ route('contact') }}" class="btn btn-primary">Contact Reservations</a>
            </div>
        @elseif($gateway === 'paystack')
            <p class="gateway-note">{{ $gatewayName }} is active. Use the secure Paystack checkout window to complete payment.</p>
            <button type="button" class="btn btn-primary" id="paystack-button">
                <i class="fa-solid fa-credit-card"></i> Pay {{ $currency }}{{ $amount }} with Paystack
            </button>
        @elseif($gateway === 'flutterwave')
            <p class="gateway-note">{{ $gatewayName }} is active. Use the secure Flutterwave checkout window to complete payment.</p>
            <button type="button" class="btn btn-primary" id="flutterwave-button">
                <i class="fa-solid fa-credit-card"></i> Pay {{ $currency }}{{ $amount }} with Flutterwave
            </button>
        @else
            <div class="gateway-alert">
                <strong>No payment gateway selected.</strong>
                <p style="margin:.35rem 0 0;">The payment screen is disabled until a real gateway is configured.</p>
            </div>
        @endif

        <form class="hidden-reference-form" action="{{ route('customer.payment.process', $booking->id) }}" method="POST" id="verified-payment-form">
            @csrf
            <input type="hidden" name="gateway_reference" id="gateway_reference">
        </form>
    </section>

    <aside class="checkout-summary">
        <h3 style="margin-bottom:1rem;">Booking Summary</h3>
        <div class="summary-row"><span>Room</span><strong>#{{ $booking->room->room_number }} {{ $booking->room->roomType->name }}</strong></div>
        <div class="summary-row"><span>Check-in</span><strong>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('M d, Y') }}</strong></div>
        <div class="summary-row"><span>Check-out</span><strong>{{ \Carbon\Carbon::parse($booking->check_out_date)->format('M d, Y') }}</strong></div>
        <div class="summary-row"><span>Status</span><strong>{{ ucfirst($booking->payment_status) }}</strong></div>
        <div class="summary-row" style="border-bottom:0;"><span>Total</span><strong style="font-size:1.4rem;color:var(--primary);">{{ $currency }}{{ $amount }}</strong></div>
    </aside>
</div>
@endsection

@section('scripts')
@if($gatewayConfigured && $gateway === 'paystack')
<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
    document.getElementById('paystack-button')?.addEventListener('click', function () {
        const handler = PaystackPop.setup({
            key: @js($gatewayPublicKey),
            email: @js(auth()->user()->email),
            amount: {{ (int) round((float) $booking->total_price * 100) }},
            currency: @js($currency),
            ref: 'BK-{{ $booking->id }}-' + Date.now(),
            callback: function(response) {
                document.getElementById('gateway_reference').value = response.reference;
                document.getElementById('verified-payment-form').submit();
            }
        });
        handler.openIframe();
    });
</script>
@elseif($gatewayConfigured && $gateway === 'flutterwave')
<script src="https://checkout.flutterwave.com/v3.js"></script>
<script>
    document.getElementById('flutterwave-button')?.addEventListener('click', function () {
        FlutterwaveCheckout({
            public_key: @js($gatewayPublicKey),
            tx_ref: 'BK-{{ $booking->id }}-' + Date.now(),
            amount: {{ (float) $booking->total_price }},
            currency: @js($currency),
            customer: {
                email: @js(auth()->user()->email),
                name: @js(auth()->user()->name),
            },
            callback: function (data) {
                document.getElementById('gateway_reference').value = data.transaction_id || data.tx_ref;
                document.getElementById('verified-payment-form').submit();
            }
        });
    });
</script>
@endif
@endsection
