<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ucfirst($documentType) }} Booking #{{ $booking->id }}</title>
    <style>
        body { margin: 0; background: #f3f4f6; color: #111827; font-family: Arial, sans-serif; }
        .page { width: min(920px, calc(100% - 32px)); margin: 32px auto; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; position: relative; }
        .page::before { content: "{{ $isSuccessful ? 'PAID' : 'NOT YET PAID' }}"; position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; transform: rotate(-28deg); font-size: clamp(56px, 13vw, 140px); font-weight: 900; letter-spacing: 0; color: {{ $isSuccessful ? 'rgba(22, 101, 52, 0.08)' : 'rgba(146, 64, 14, 0.10)' }}; pointer-events: none; z-index: 0; text-align: center; }
        .page > * { position: relative; z-index: 1; }
        .header { display: flex; justify-content: space-between; gap: 24px; padding: 32px; background: #111827; color: #fff; }
        .brand h1 { margin: 0 0 8px; font-size: 24px; }
        .brand p, .meta p { margin: 4px 0; color: #d1d5db; font-size: 13px; }
        .badge { display: inline-block; padding: 6px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; text-transform: uppercase; background: {{ $isSuccessful ? '#dcfce7' : '#fef3c7' }}; color: {{ $isSuccessful ? '#166534' : '#92400e' }}; }
        .section { padding: 24px 32px; border-bottom: 1px solid #e5e7eb; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        h2 { margin: 0 0 12px; font-size: 15px; color: #374151; text-transform: uppercase; }
        p { margin: 5px 0; line-height: 1.5; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 12px; color: #6b7280; text-transform: uppercase; padding: 12px 0; border-bottom: 1px solid #e5e7eb; }
        td { padding: 14px 0; border-bottom: 1px solid #f3f4f6; }
        .right { text-align: right; }
        .total { display: flex; justify-content: flex-end; padding-top: 18px; }
        .total-box { min-width: 280px; }
        .total-row { display: flex; justify-content: space-between; padding: 8px 0; }
        .grand { font-size: 20px; font-weight: 800; border-top: 2px solid #111827; margin-top: 8px; padding-top: 12px; }
        .actions { width: min(920px, calc(100% - 32px)); margin: 0 auto 32px; display: flex; justify-content: flex-end; }
        button, .pay-link { border: 0; background: #111827; color: #fff; border-radius: 6px; padding: 10px 14px; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-block; }
        .pay-link { background: #166534; margin-top: 12px; }
        @media print { body { background: #fff; } .page { width: 100%; margin: 0; border: 0; } .actions { display: none; } }
        @media (max-width: 700px) { .header, .grid { grid-template-columns: 1fr; display: grid; } .header, .section { padding: 22px; } }
    </style>
</head>
<body>
    @php
        $nights = max(1, $booking->check_in_date->diffInDays($booking->check_out_date));
        $latestPayment = $booking->payments->sortByDesc('id')->first();
    @endphp
    <div class="page">
        <div class="header">
            <div class="brand">
                <h1>{{ \App\Models\Setting::getValue('hotel_name', 'Aetheria Grand Hotel') }}</h1>
                <p>{{ \App\Models\Setting::getValue('physical_address', 'Golden Coast Beach, Suite A') }}</p>
                <p>{{ \App\Models\Setting::getValue('contact_phone', '+1 (555) 123-4567') }} · {{ \App\Models\Setting::getValue('contact_email', 'info@aetheriagrand.com') }}</p>
            </div>
            <div class="meta right">
                <span class="badge">{{ ucfirst($documentType) }}</span>
                <p><strong>BK-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</strong></p>
                <p>{{ $booking->created_at?->format('M d, Y h:i A') }}</p>
            </div>
        </div>

        <div class="section grid">
            <div>
                <h2>Guest</h2>
                <p><strong>{{ $booking->customer->name ?? 'Guest' }}</strong></p>
                <p>{{ $booking->customer->email ?? '' }}</p>
                @if($booking->customer?->phone)<p>{{ $booking->customer->phone }}</p>@endif
            </div>
            <div>
                <h2>Booking Details</h2>
                <p>Room: <strong>#{{ $booking->room->room_number ?? 'N/A' }} {{ $booking->room->roomType->name ?? '' }}</strong></p>
                <p>Check-in: <strong>{{ $booking->check_in_date->format('M d, Y') }}</strong></p>
                <p>Check-out: <strong>{{ $booking->check_out_date->format('M d, Y') }}</strong></p>
                <p>Status: <strong>{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</strong></p>
                <p>Payment: <strong>{{ ucfirst($booking->payment_status) }}</strong></p>
            </div>
        </div>

        <div class="section">
            <h2>Charges</h2>
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="right">Nights</th>
                        <th class="right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $booking->room->roomType->name ?? 'Room Booking' }}</td>
                        <td class="right">{{ $nights }}</td>
                        <td class="right">{{ $currency }}{{ number_format((float) $booking->total_price + (float) $booking->discount_amount + (float) $booking->loyalty_discount_amount, 2) }}</td>
                    </tr>
                    @if($booking->discount_amount > 0)
                        <tr>
                            <td>Coupon discount {{ $booking->coupon_code ? '(' . $booking->coupon_code . ')' : '' }}</td>
                            <td class="right"></td>
                            <td class="right">-{{ $currency }}{{ number_format((float) $booking->discount_amount, 2) }}</td>
                        </tr>
                    @endif
                    @if($booking->loyalty_discount_amount > 0)
                        <tr>
                            <td>Loyalty discount</td>
                            <td class="right"></td>
                            <td class="right">-{{ $currency }}{{ number_format((float) $booking->loyalty_discount_amount, 2) }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
            <div class="total">
                <div class="total-box">
                    <div class="total-row"><span>Payment method</span><span>{{ $latestPayment ? ucwords(str_replace('_', ' ', $latestPayment->payment_method)) : 'Pending' }}</span></div>
                    @if($latestPayment)
                        <div class="total-row"><span>Transaction</span><span>{{ $latestPayment->transaction_id }}</span></div>
                    @endif
                    <div class="total-row grand"><span>Total</span><span>{{ $currency }}{{ number_format((float) $booking->total_price, 2) }}</span></div>
                    @if(!$isSuccessful)
                        <p style="font-size:13px;color:#6b7280;">This invoice remains payable until the booking payment is completed.</p>
                        @if(Route::has('customer.payment'))
                            <a class="pay-link" href="{{ route('customer.payment', $booking) }}">Make Payment</a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="actions">
        <button onclick="window.print()">Print {{ ucfirst($documentType) }}</button>
    </div>
</body>
</html>
