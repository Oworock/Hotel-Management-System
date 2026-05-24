<?php

namespace Plugins\EcommerceTuckShop\Http\Controllers;

use App\Http\Controllers\Controller;
use Plugins\EcommerceTuckShop\Models\Product;
use Plugins\EcommerceTuckShop\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;

class AdminProductOrderController extends Controller
{
    public function products(Request $request)
    {
        $user = auth()->user();
        $restrictedType = null;
        if (!$user->isAdmin() && !$user->isSuperAdmin()) {
            if ($user->role === 'tuck_shop_manager' || ($user->hasFunction('manage_tuck_shop') && !$user->hasFunction('manage_restaurant'))) {
                $restrictedType = 'tuck_shop';
            } elseif ($user->role === 'restaurant_manager' || ($user->hasFunction('manage_restaurant') && !$user->hasFunction('manage_tuck_shop'))) {
                $restrictedType = 'restaurant';
            }
        }

        $query = Product::query();
        if ($restrictedType) {
            $query->where('type', $restrictedType);
        } elseif ($request->filled('type') && in_array($request->type, ['tuck_shop', 'restaurant'], true)) {
            $query->where('type', $request->type);
        }

        $products = $query->orderBy('type')->orderBy('name')->get();
        $currentType = $restrictedType ?: $request->query('type', 'all');
        return view('admin.products', compact('products', 'currentType', 'restrictedType'));
    }

    public function storeProduct(Request $request)
    {
        $user = auth()->user();
        $restrictedType = null;
        if (!$user->isAdmin() && !$user->isSuperAdmin()) {
            if ($user->role === 'tuck_shop_manager' || ($user->hasFunction('manage_tuck_shop') && !$user->hasFunction('manage_restaurant'))) {
                $restrictedType = 'tuck_shop';
            } elseif ($user->role === 'restaurant_manager' || ($user->hasFunction('manage_restaurant') && !$user->hasFunction('manage_tuck_shop'))) {
                $restrictedType = 'restaurant';
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'type' => 'required|in:tuck_shop,restaurant',
            'category' => 'nullable|string|max:255',
            'image_path' => 'nullable|string|max:500',
            'is_available' => 'nullable'
        ]);

        if ($restrictedType && $request->type !== $restrictedType) {
            abort(403, 'Unauthorized. You cannot manage products of type: ' . $request->type);
        }

        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'type' => $request->type,
            'category' => $request->category ?? 'General',
            'image_path' => $request->image_path,
            'is_available' => $request->has('is_available'),
        ]);

        return redirect()->back()->with('success', 'Product added successfully.');
    }

    public function updateProduct(Request $request, Product $product)
    {
        $user = auth()->user();
        $restrictedType = null;
        if (!$user->isAdmin() && !$user->isSuperAdmin()) {
            if ($user->role === 'tuck_shop_manager' || ($user->hasFunction('manage_tuck_shop') && !$user->hasFunction('manage_restaurant'))) {
                $restrictedType = 'tuck_shop';
            } elseif ($user->role === 'restaurant_manager' || ($user->hasFunction('manage_restaurant') && !$user->hasFunction('manage_tuck_shop'))) {
                $restrictedType = 'restaurant';
            }
        }

        if ($restrictedType && ($product->type !== $restrictedType || $request->type !== $restrictedType)) {
            abort(403, 'Unauthorized. You cannot manage products of type: ' . $request->type);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'type' => 'required|in:tuck_shop,restaurant',
            'category' => 'nullable|string|max:255',
            'image_path' => 'nullable|string|max:500',
            'is_available' => 'nullable'
        ]);

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'type' => $request->type,
            'category' => $request->category ?? 'General',
            'image_path' => $request->image_path,
            'is_available' => $request->has('is_available'),
        ]);

        return redirect()->back()->with('success', 'Product updated successfully.');
    }

    public function deleteProduct(Product $product)
    {
        $user = auth()->user();
        $restrictedType = null;
        if (!$user->isAdmin() && !$user->isSuperAdmin()) {
            if ($user->role === 'tuck_shop_manager' || ($user->hasFunction('manage_tuck_shop') && !$user->hasFunction('manage_restaurant'))) {
                $restrictedType = 'tuck_shop';
            } elseif ($user->role === 'restaurant_manager' || ($user->hasFunction('manage_restaurant') && !$user->hasFunction('manage_tuck_shop'))) {
                $restrictedType = 'restaurant';
            }
        }

        if ($restrictedType && $product->type !== $restrictedType) {
            abort(403, 'Unauthorized. You cannot delete products of type: ' . $product->type);
        }

        $product->delete();
        return redirect()->back()->with('success', 'Product deleted successfully.');
    }

    public function orders(Request $request)
    {
        $user = auth()->user();
        $restrictedType = null;
        if (!$user->isAdmin() && !$user->isSuperAdmin()) {
            if ($user->role === 'tuck_shop_manager' || ($user->hasFunction('manage_tuck_shop') && !$user->hasFunction('manage_restaurant'))) {
                $restrictedType = 'tuck_shop';
            } elseif ($user->role === 'restaurant_manager' || ($user->hasFunction('manage_restaurant') && !$user->hasFunction('manage_tuck_shop'))) {
                $restrictedType = 'restaurant';
            }
        }

        $query = Order::with(['orderItems.product', 'user']);

        if ($restrictedType) {
            $query->whereHas('orderItems.product', function($q) use ($restrictedType) {
                $q->where('type', $restrictedType);
            });
        } elseif ($request->filled('type') && in_array($request->type, ['tuck_shop', 'restaurant'], true)) {
            $type = $request->type;
            $query->whereHas('orderItems.product', function($q) use ($type) {
                $q->where('type', $type);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('id', 'desc')->paginate(15);
        $currency = Setting::getValue('currency', 'USD');
        $currentType = $restrictedType ?: $request->query('type', 'all');
        return view('admin.orders', compact('orders', 'currency', 'currentType', 'restrictedType'));
    }

    public function updateOrderStatus(Order $order, Request $request)
    {
        $user = auth()->user();
        $restrictedType = null;
        if (!$user->isAdmin() && !$user->isSuperAdmin()) {
            if ($user->role === 'tuck_shop_manager' || ($user->hasFunction('manage_tuck_shop') && !$user->hasFunction('manage_restaurant'))) {
                $restrictedType = 'tuck_shop';
            } elseif ($user->role === 'restaurant_manager' || ($user->hasFunction('manage_restaurant') && !$user->hasFunction('manage_tuck_shop'))) {
                $restrictedType = 'restaurant';
            }
        }

        if ($restrictedType) {
            $hasPermittedProduct = $order->orderItems()->whereHas('product', function($q) use ($restrictedType) {
                $q->where('type', $restrictedType);
            })->exists();

            if (!$hasPermittedProduct) {
                abort(403, 'Unauthorized. You cannot manage this order.');
            }
        }

        $request->validate([
            'status' => 'required|in:pending,preparing,delivered,cancelled',
        ]);

        $order->update([
            'status' => $request->status,
        ]);

        if ($request->status === 'delivered' && $order->payment_method === 'cash_on_delivery') {
            $order->update(['payment_status' => 'paid']);
        }

        return redirect()->back()->with('success', 'Order status updated.');
    }

    public function orderDocument(Order $order)
    {
        $this->authorizeOrderAccess($order);

        $order->load(['orderItems.product', 'user', 'booking.room.roomType']);
        $currency = Setting::getValue('currency', 'USD');
        $isSuccessful = $order->payment_status === 'paid'
            || $order->payment_method === 'room_charge'
            || $order->status === 'delivered';
        $documentType = $isSuccessful ? 'receipt' : 'invoice';

        return view('admin.order-document', compact('order', 'currency', 'documentType', 'isSuccessful'));
    }

    protected function authorizeOrderAccess(Order $order): void
    {
        $user = auth()->user();
        $restrictedType = null;

        if (!$user->isAdmin() && !$user->isSuperAdmin()) {
            if ($user->role === 'tuck_shop_manager' || ($user->hasFunction('manage_tuck_shop') && !$user->hasFunction('manage_restaurant'))) {
                $restrictedType = 'tuck_shop';
            } elseif ($user->role === 'restaurant_manager' || ($user->hasFunction('manage_restaurant') && !$user->hasFunction('manage_tuck_shop'))) {
                $restrictedType = 'restaurant';
            }
        }

        if (!$restrictedType) {
            return;
        }

        $hasPermittedProduct = $order->orderItems()->whereHas('product', function($q) use ($restrictedType) {
            $q->where('type', $restrictedType);
        })->exists();

        abort_unless($hasPermittedProduct, 403, 'Unauthorized. You cannot view this order document.');
    }
}
