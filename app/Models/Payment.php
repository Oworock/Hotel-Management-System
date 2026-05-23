<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['booking_id', 'amount', 'payment_method', 'transaction_id', 'status'])]
class Payment extends Model
{
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    protected static function booted()
    {
        static::saved(function ($payment) {
            \App\Models\WebhookSubscription::dispatchEvent('payment.recorded', [
                'payment_id' => $payment->id,
                'booking_id' => $payment->booking_id,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'transaction_id' => $payment->transaction_id,
                'status' => $payment->status,
                'created_at' => $payment->created_at ? $payment->created_at->toIso8601String() : null,
            ]);
        });
    }
}
