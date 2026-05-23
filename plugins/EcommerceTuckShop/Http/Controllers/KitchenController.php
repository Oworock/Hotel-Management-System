<?php

namespace Plugins\EcommerceTuckShop\Http\Controllers;

use App\Http\Controllers\Controller;
use Plugins\EcommerceTuckShop\Models\Order;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    public function dashboard()
    {
        // Fetch pending and preparing orders. Eager load orderItems and products to filter or show dishes
        $orders = Order::with(['orderItems.product'])
            ->whereIn('status', ['pending', 'preparing'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('kitchen.dashboard', compact('orders'));
    }

    public function updateStatus(Order $order, Request $request)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,delivered,cancelled',
        ]);

        $order->update([
            'status' => $request->status,
        ]);

        // If delivered, we can also set payment_status to paid if payment was cash on delivery
        if ($request->status === 'delivered' && $order->payment_method === 'cash_on_delivery') {
            $order->update(['payment_status' => 'paid']);
        }

        return redirect()->route('kitchen.dashboard')->with('success', 'Order status updated successfully.');
    }
}
