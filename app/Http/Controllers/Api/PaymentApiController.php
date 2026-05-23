<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentApiController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::all();

        return response()->json([
            'status' => 'success',
            'data' => $payments
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'transaction_id' => 'nullable|string',
        ]);

        $booking = Booking::find($request->booking_id);

        $payment = Payment::create([
            'booking_id' => $request->booking_id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'transaction_id' => $request->transaction_id ?? 'TXN-' . strtoupper(bin2hex(random_bytes(6))),
            'status' => 'completed',
        ]);

        // Automatically mark booking as paid and confirmed
        $booking->update([
            'payment_status' => 'paid',
            'status' => 'confirmed',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Payment recorded successfully.',
            'data' => $payment
        ], 201);
    }
}
