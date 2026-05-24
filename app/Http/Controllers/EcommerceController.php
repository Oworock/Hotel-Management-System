<?php

namespace App\Http\Controllers;

use App\Models\ShopItem;
use App\Models\RestaurantItem;
use App\Models\Order;
use Illuminate\Http\Request;

class EcommerceController extends Controller
{
    // SHOP MANAGEMENT
    public function shopDashboard()
    {
        $totalItems = ShopItem::count();
        $lowStockItems = ShopItem::where('stock', '<=', 'reorder_level')->count();
        $totalSales = Order::where('type', 'shop')->sum('total');
        $pendingOrders = Order::where('type', 'shop')->where('status', 'pending')->count();
        
        return view('admin.ecommerce.shop-dashboard', compact('totalItems', 'lowStockItems', 'totalSales', 'pendingOrders'));
    }

    public function shopItems()
    {
        $items = ShopItem::paginate(15);
        return view('admin.ecommerce.shop-items', compact('items'));
    }

    public function createShopItem()
    {
        return view('admin.ecommerce.shop-item-form');
    }

    public function storeShopItem(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'category' => 'required|string',
            'sku' => 'required|unique:shop_items|string',
            'stock' => 'required|integer|min:0',
            'reorder_level' => 'integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('shop', 'public');
        }

        ShopItem::create($validated);
        return redirect()->route('admin.shop.items')->with('success', 'Shop item created successfully');
    }

    public function editShopItem(ShopItem $item)
    {
        return view('admin.ecommerce.shop-item-form', compact('item'));
    }

    public function updateShopItem(Request $request, ShopItem $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'category' => 'required|string',
            'sku' => "required|unique:shop_items,sku,{$item->id}|string",
            'stock' => 'required|integer|min:0',
            'reorder_level' => 'integer|min:0',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            \Storage::disk('public')->delete($item->image);
            $validated['image'] = $request->file('image')->store('shop', 'public');
        }

        $item->update($validated);
        return back()->with('success', 'Shop item updated successfully');
    }

    public function deleteShopItem(ShopItem $item)
    {
        if ($item->image) {
            \Storage::disk('public')->delete($item->image);
        }
        $item->delete();
        return back()->with('success', 'Shop item deleted successfully');
    }

    // RESTAURANT MANAGEMENT
    public function restaurantDashboard()
    {
        $totalItems = RestaurantItem::count();
        $vegetarianItems = RestaurantItem::where('is_vegetarian', true)->count();
        $totalSales = Order::where('type', 'restaurant')->sum('total');
        $pendingOrders = Order::where('type', 'restaurant')->where('status', 'pending')->count();
        
        return view('admin.ecommerce.restaurant-dashboard', compact('totalItems', 'vegetarianItems', 'totalSales', 'pendingOrders'));
    }

    public function restaurantItems()
    {
        $items = RestaurantItem::paginate(15);
        return view('admin.ecommerce.restaurant-items', compact('items'));
    }

    public function createRestaurantItem()
    {
        return view('admin.ecommerce.restaurant-item-form');
    }

    public function storeRestaurantItem(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'category' => 'required|string',
            'calories' => 'nullable|integer|min:0',
            'allergens' => 'nullable|string',
            'preparation_time' => 'integer|min:5',
            'is_vegetarian' => 'boolean',
            'is_vegan' => 'boolean',
            'is_spicy' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('restaurant', 'public');
        }

        RestaurantItem::create($validated);
        return redirect()->route('admin.restaurant.items')->with('success', 'Restaurant item created successfully');
    }

    public function editRestaurantItem(RestaurantItem $item)
    {
        return view('admin.ecommerce.restaurant-item-form', compact('item'));
    }

    public function updateRestaurantItem(Request $request, RestaurantItem $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'category' => 'required|string',
            'calories' => 'nullable|integer|min:0',
            'allergens' => 'nullable|string',
            'preparation_time' => 'integer|min:5',
            'is_vegetarian' => 'boolean',
            'is_vegan' => 'boolean',
            'is_spicy' => 'boolean',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            \Storage::disk('public')->delete($item->image);
            $validated['image'] = $request->file('image')->store('restaurant', 'public');
        }

        $item->update($validated);
        return back()->with('success', 'Restaurant item updated successfully');
    }

    public function deleteRestaurantItem(RestaurantItem $item)
    {
        if ($item->image) {
            \Storage::disk('public')->delete($item->image);
        }
        $item->delete();
        return back()->with('success', 'Restaurant item deleted successfully');
    }

    // ORDERS MANAGEMENT
    public function orders($type = null)
    {
        $query = Order::latest();
        
        if ($type) {
            $query->where('type', $type);
        }
        
        $orders = $query->paginate(15);
        return view('admin.ecommerce.orders', compact('orders', 'type'));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,preparing,ready,completed,cancelled',
        ]);

        $order->update($validated);
        
        if ($validated['status'] === 'completed') {
            $order->update(['completed_at' => now()]);
        }

        return back()->with('success', 'Order status updated successfully');
    }

    // FRONTEND - SHOP
    public function shop()
    {
        $items = ShopItem::where('is_active', true)->where('is_available', true)->paginate(12);
        $categories = ShopItem::where('is_active', true)->distinct()->pluck('category');
        return view('shop.index', compact('items', 'categories'));
    }

    public function shopByCategory($category)
    {
        $items = ShopItem::where('category', $category)->where('is_active', true)->where('is_available', true)->paginate(12);
        return view('shop.category', compact('items', 'category'));
    }

    // FRONTEND - RESTAURANT
    public function restaurant()
    {
        $items = RestaurantItem::where('is_active', true)->where('is_available', true)->paginate(12);
        $categories = RestaurantItem::where('is_active', true)->distinct()->pluck('category');
        return view('restaurant.index', compact('items', 'categories'));
    }

    public function restaurantByCategory($category)
    {
        $items = RestaurantItem::where('category', $category)->where('is_active', true)->where('is_available', true)->paginate(12);
        return view('restaurant.category', compact('items', 'category'));
    }

    // CART & CHECKOUT
    public function cart()
    {
        $cartItems = session()->get('cart', []);
        $total = array_sum(array_column($cartItems, 'total'));
        return view('ecommerce.cart', compact('cartItems', 'total'));
    }

    public function addToCart(Request $request)
    {
        $cart = session()->get('cart', []);
        $type = $request->input('type'); // 'shop' or 'restaurant'
        $id = $request->input('id');
        $quantity = $request->input('quantity', 1);

        $item = $type === 'shop' 
            ? ShopItem::find($id)
            : RestaurantItem::find($id);

        if ($item) {
            $key = "{$type}_{$id}";
            $cart[$key] = [
                'id' => $id,
                'type' => $type,
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => $quantity,
                'total' => $item->price * $quantity,
            ];
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Item added to cart');
    }

    public function removeFromCart($key)
    {
        $cart = session()->get('cart', []);
        unset($cart[$key]);
        session()->put('cart', $cart);
        return back()->with('success', 'Item removed from cart');
    }

    public function checkout(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('shop')->with('error', 'Your cart is empty');
        }

        $validated = $request->validate([
            'type' => 'required|in:shop,restaurant',
            'delivery_mode' => 'required|in:room_delivery,pickup,table_service',
            'room_number' => 'nullable|integer',
            'payment_method' => 'required|in:room_charge,cash,card,online',
            'special_instructions' => 'nullable|string',
        ]);

        $subtotal = array_sum(array_column($cart, 'total'));
        $tax = $subtotal * 0.1; // 10% tax
        $total = $subtotal + $tax;

        $order = Order::create([
            'booking_id' => auth()->check() ? auth()->user()->booking_id : null,
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'delivery_mode' => $validated['delivery_mode'],
            'room_number' => $validated['room_number'],
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'payment_method' => $validated['payment_method'],
            'special_instructions' => $validated['special_instructions'],
        ]);

        session()->forget('cart');
        return redirect()->route('order.confirmation', $order)->with('success', 'Order placed successfully');
    }

    public function orderConfirmation(Order $order)
    {
        return view('ecommerce.confirmation', compact('order'));
    }
}
