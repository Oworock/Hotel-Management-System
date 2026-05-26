<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;

class PaymentApiController extends Controller
{
    public function index(Request $request)
    {
        if (!$this->userCanManagePayments($request)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden: this API token cannot view payments.',
            ], 403);
        }

        $payments = Payment::all();

        return response()->json([
            'status' => 'success',
            'data' => $payments
        ]);
    }

    public function store(Request $request, PaymentGatewayService $paymentGateway)
    {
        if (!$this->userCanManagePayments($request)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden: this API token cannot record payments.',
            ], 403);
        }

        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|in:cash,pos,bank_transfer,paystack,flutterwave',
            'transaction_id' => 'nullable|string|max:255',
            'gateway_reference' => 'required_if:payment_method,paystack,flutterwave|nullable|string|max:255',
        ]);

        $booking = Booking::find($request->booking_id);
        if ($booking->payment_status === 'paid') {
            return response()->json([
                'status' => 'error',
                'message' => 'This booking is already paid.',
            ], 422);
        }

        if (abs((float) $request->amount - (float) $booking->total_price) > 0.01) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment amount must match the booking total.',
            ], 422);
        }

        $transactionId = $request->transaction_id ?? 'TXN-' . strtoupper(bin2hex(random_bytes(6)));
        $method = $request->payment_method;

        if (in_array($request->payment_method, ['paystack', 'flutterwave'], true)) {
            $verification = $paymentGateway->verify($request->payment_method, $request->gateway_reference, $booking);
            if (!$verification['ok']) {
                return response()->json([
                    'status' => 'error',
                    'message' => $verification['message'],
                ], 422);
            }

            $transactionId = $verification['reference'];
            $method = $verification['method'];
        }

        $payment = Payment::create([
            'booking_id' => $request->booking_id,
            'amount' => $booking->total_price,
            'payment_method' => $method,
            'transaction_id' => $transactionId,
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

    private function userCanManagePayments(Request $request): bool
    {
        $user = $request->user();

        return $user
            && ($user->isSuperAdmin()
                || $user->isAdmin()
                || $user->hasFunction('manage_payments'));
    }
}
