<?php

namespace Plugins\EcommerceTuckShop\Http\Controllers;

use App\Http\Controllers\Controller;
use Plugins\EcommerceTuckShop\Models\Product;
use Plugins\EcommerceTuckShop\Models\Order;
use Plugins\EcommerceTuckShop\Models\OrderItem;
use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $this->ensureChannelEnabled('tuck_shop');
        $products = Product::where('type', 'tuck_shop')->where('is_available', true)->get();
        $cart = session()->get('cart', []);
        $currency = Setting::getValue('currency', 'USD');
        return view('shop.index', compact('products', 'cart', 'currency'));
    }

    public function restaurantIndex()
    {
        $this->ensureChannelEnabled('restaurant');
        $products = Product::where('type', 'restaurant')->where('is_available', true)->get();
        $cart = session()->get('cart', []);
        $currency = Setting::getValue('currency', 'USD');
        return view('shop.restaurant', compact('products', 'cart', 'currency'));
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        if (!$product->is_available) {
            return redirect()->back()->with('error', 'Product is currently unavailable.');
        }
        $this->ensureChannelEnabled($product->type);

        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $request->quantity;
        } else {
            $cart[$product->id] = [
                'name' => $product->name,
                'quantity' => (int)$request->quantity,
                'price' => (float)$product->price,
                'type' => $product->type,
                'image_path' => $product->image_path
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', "{$product->name} added to cart!");
    }

    public function removeFromCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required'
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->product_id])) {
            unset($cart[$request->product_id]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Item removed from cart.');
    }

    public function checkoutForm()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('shop.index')->with('error', 'Your cart is empty.');
        }
        $this->ensureCartChannelsEnabled($cart);

        $currency = Setting::getValue('currency', 'USD');
        $taxRate = (float)Setting::getValue('ecommerce_tax_rate', Setting::getValue('tax_rate', '12'));

        // Check if current user is an active checked-in guest
        $activeBooking = null;
        if (auth()->check()) {
            $activeBooking = Booking::where('customer_id', auth()->id())
                ->where('status', 'checked_in')
                ->first();
        }

        return view('shop.checkout', compact('cart', 'currency', 'taxRate', 'activeBooking'));
    }

    public function checkout(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('shop.index')->with('error', 'Your cart is empty.');
        }
        $this->ensureCartChannelsEnabled($cart);

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email',
            'customer_phone' => 'required|string|max:50',
            'delivery_type' => 'required|in:room,takeaway,address',
            'delivery_details' => 'required_if:delivery_type,room,address|string|nullable',
            'payment_method' => 'required|in:cash_on_delivery,card,room_charge',
        ]);

        $activeBooking = null;
        if (auth()->check()) {
            $activeBooking = Booking::where('customer_id', auth()->id())
                ->where('status', 'checked_in')
                ->first();
        }

        if ($request->payment_method === 'room_charge') {
            if (!$activeBooking) {
                return redirect()->back()->with('error', 'You must be a checked-in guest to charge to room.');
            }
        }

        // Calculate pricing
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        $taxRate = (float)Setting::getValue('ecommerce_tax_rate', Setting::getValue('tax_rate', '12'));
        $shippingFeeSetting = (float)Setting::getValue('ecommerce_shipping_fee', '5.00');
        $freeShippingThreshold = (float)Setting::getValue('ecommerce_free_shipping_limit', '50.00');

        $shippingFee = ($subtotal >= $freeShippingThreshold) ? 0.00 : $shippingFeeSetting;
        $taxAmount = $subtotal * ($taxRate / 100);
        $totalPrice = $subtotal + $taxAmount + $shippingFee;

        $paymentStatus = 'unpaid';
        if ($request->payment_method === 'card') {
            $paymentStatus = 'paid';
        }

        // Create order
        $order = Order::create([
            'user_id' => auth()->check() ? auth()->id() : null,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'delivery_type' => $request->delivery_type,
            'delivery_details' => $request->delivery_details ?? 'Takeaway order',
            'total_price' => $totalPrice,
            'payment_method' => $request->payment_method,
            'payment_status' => $paymentStatus,
            'status' => 'pending',
            'booking_id' => $activeBooking ? $activeBooking->id : null,
        ]);

        // Add order items
        foreach ($cart as $productId => $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $productId,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        // If Charge to Room is selected, we also log this under the Booking bill total if needed
        if ($request->payment_method === 'room_charge' && $activeBooking) {
            $activeBooking->increment('total_price', $totalPrice);
        }

        // Clear cart
        session()->forget('cart');

        return redirect()->route('shop.index')->with('success', 'Order placed successfully! Reference #' . $order->id);
    }

    protected function ensureChannelEnabled(string $type): void
    {
        $storeOpen = Setting::getValue('ecommerce_store_status', 'open') === 'open';
        $settingKey = $type === 'restaurant' ? 'ecommerce_restaurant_enabled' : 'ecommerce_tuck_shop_enabled';

        abort_unless($storeOpen && Setting::getValue($settingKey, '1') === '1', 404);
    }

    protected function ensureCartChannelsEnabled(array $cart): void
    {
        foreach ($cart as $item) {
            $this->ensureChannelEnabled($item['type'] ?? 'tuck_shop');
        }
    }
}
