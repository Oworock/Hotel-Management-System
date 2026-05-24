<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class PaymentGatewayService
{
    public function activeGateway(): string
    {
        $gateway = Setting::getValue('active_payment_gateway', 'disabled') ?: 'disabled';

        return in_array($gateway, ['paystack', 'flutterwave'], true) ? $gateway : 'disabled';
    }

    public function publicKey(?string $gateway = null): ?string
    {
        $gateway ??= $this->activeGateway();

        return match ($gateway) {
            'paystack' => Setting::getValue('paystack_public_key'),
            'flutterwave' => Setting::getValue('flutterwave_public_key'),
            default => null,
        };
    }

    public function isConfigured(?string $gateway = null): bool
    {
        $gateway ??= $this->activeGateway();

        return match ($gateway) {
            'paystack' => $this->hasUsableKey('paystack_public_key', 'pk_')
                && $this->hasUsableKey('paystack_secret_key', 'sk_'),
            'flutterwave' => $this->hasUsableKey('flutterwave_public_key', 'flwpubk')
                && $this->hasUsableKey('flutterwave_secret_key', 'flwseck'),
            default => false,
        };
    }

    public function verify(string $gateway, string $reference, Booking $booking): array
    {
        if (!$this->isConfigured($gateway)) {
            return ['ok' => false, 'message' => 'Payment gateway is not configured.'];
        }

        return match ($gateway) {
            'paystack' => $this->verifyPaystack($reference, $booking),
            'flutterwave' => $this->verifyFlutterwave($reference, $booking),
            default => ['ok' => false, 'message' => 'No active payment gateway is configured.'],
        };
    }

    protected function verifyPaystack(string $reference, Booking $booking): array
    {
        $response = Http::withToken((string) Setting::getValue('paystack_secret_key'))
            ->acceptJson()
            ->timeout(20)
            ->get('https://api.paystack.co/transaction/verify/' . urlencode($reference));

        if (!$response->ok() || $response->json('data.status') !== 'success') {
            return ['ok' => false, 'message' => 'Paystack could not verify this transaction.'];
        }

        $paidAmount = ((float) $response->json('data.amount')) / 100;

        if ($paidAmount + 0.01 < (float) $booking->total_price) {
            return ['ok' => false, 'message' => 'The verified Paystack amount is lower than the booking total.'];
        }

        return [
            'ok' => true,
            'reference' => $response->json('data.reference') ?: $reference,
            'method' => 'Paystack',
        ];
    }

    protected function verifyFlutterwave(string $reference, Booking $booking): array
    {
        $response = Http::withToken((string) Setting::getValue('flutterwave_secret_key'))
            ->acceptJson()
            ->timeout(20)
            ->get('https://api.flutterwave.com/v3/transactions/' . urlencode($reference) . '/verify');

        if (!$response->ok() || $response->json('data.status') !== 'successful') {
            return ['ok' => false, 'message' => 'Flutterwave could not verify this transaction.'];
        }

        $paidAmount = (float) $response->json('data.amount');

        if ($paidAmount + 0.01 < (float) $booking->total_price) {
            return ['ok' => false, 'message' => 'The verified Flutterwave amount is lower than the booking total.'];
        }

        return [
            'ok' => true,
            'reference' => $response->json('data.tx_ref') ?: $reference,
            'method' => 'Flutterwave',
        ];
    }

    protected function hasUsableKey(string $settingKey, string $prefix): bool
    {
        $value = trim((string) Setting::getValue($settingKey));

        $normalized = strtolower($value);

        return $value !== ''
            && !str_contains($normalized, 'demo')
            && str_starts_with($normalized, strtolower($prefix));
    }
}
