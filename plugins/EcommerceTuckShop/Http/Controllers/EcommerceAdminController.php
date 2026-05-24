<?php

namespace Plugins\EcommerceTuckShop\Http\Controllers;

use App\Http\Controllers\Controller;
use Plugins\EcommerceTuckShop\Models\Order;
use Plugins\EcommerceTuckShop\Models\OrderItem;
use Plugins\EcommerceTuckShop\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EcommerceAdminController extends Controller
{
    public function dashboard()
    {
        $currency = Setting::getValue('currency', 'USD');

        $totalOrders = Order::count();
        
        $totalRevenue = Order::where(function ($q) {
            $q->where('payment_status', 'paid')
              ->orWhere('payment_method', 'room_charge')
              ->orWhere('status', 'delivered');
        })->sum('total_price');

        $averageOrderValue = $totalOrders > 0 ? ($totalRevenue / $totalOrders) : 0;
        $pendingOrdersCount = Order::where('status', 'pending')->count();
        $recentOrders = Order::orderBy('id', 'desc')->take(10)->get();

        $totalProducts = Product::count();
        $availableProducts = Product::where('is_available', true)->count();

        return view('ecommerce.dashboard', compact(
            'currency',
            'totalOrders',
            'totalRevenue',
            'averageOrderValue',
            'pendingOrdersCount',
            'recentOrders',
            'totalProducts',
            'availableProducts'
        ));
    }

    public function analytics()
    {
        $currency = Setting::getValue('currency', 'USD');

        // Revenue by Product Type (tuck_shop vs restaurant)
        $typeRevenueRaw = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where(function ($q) {
                $q->where('orders.payment_status', 'paid')
                  ->orWhere('orders.payment_method', 'room_charge')
                  ->orWhere('orders.status', 'delivered');
            })
            ->select('products.type', DB::raw('SUM(order_items.price * order_items.quantity) as total'))
            ->groupBy('products.type')
            ->pluck('total', 'type')
            ->toArray();

        $typeRevenue = [
            'tuck_shop' => (float)($typeRevenueRaw['tuck_shop'] ?? 0),
            'restaurant' => (float)($typeRevenueRaw['restaurant'] ?? 0)
        ];

        // Sales by Category
        $categorySales = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where(function ($q) {
                $q->where('orders.payment_status', 'paid')
                  ->orWhere('orders.payment_method', 'room_charge')
                  ->orWhere('orders.status', 'delivered');
            })
            ->select('products.category', DB::raw('SUM(order_items.price * order_items.quantity) as total'))
            ->groupBy('products.category')
            ->pluck('total', 'category')
            ->toArray();

        // Orders by Status
        $statusCounts = Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Daily Revenue Trend (Last 30 Days)
        $dailyRaw = Order::where(function ($q) {
                $q->where('payment_status', 'paid')
                  ->orWhere('payment_method', 'room_charge')
                  ->orWhere('status', 'delivered');
            })
            ->select(DB::raw("strftime('%Y-%m-%d', created_at) as date"), DB::raw('SUM(total_price) as total'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->take(30)
            ->get();

        return view('ecommerce.analytics', compact(
            'currency',
            'typeRevenue',
            'categorySales',
            'statusCounts',
            'dailyRaw'
        ));
    }

    public function settingsForm()
    {
        $settings = [
            'ecommerce_store_name' => Setting::getValue('ecommerce_store_name', 'StayFlow Tuck Shop'),
            'ecommerce_store_email' => Setting::getValue('ecommerce_store_email', 'shop@stayflow.com'),
            'ecommerce_shipping_fee' => Setting::getValue('ecommerce_shipping_fee', '5.00'),
            'ecommerce_free_shipping_limit' => Setting::getValue('ecommerce_free_shipping_limit', '50.00'),
            'ecommerce_tax_rate' => Setting::getValue('ecommerce_tax_rate', Setting::getValue('tax_rate', '12')),
            'ecommerce_store_status' => Setting::getValue('ecommerce_store_status', 'open'),
            'ecommerce_tuck_shop_enabled' => Setting::getValue('ecommerce_tuck_shop_enabled', '1'),
            'ecommerce_restaurant_enabled' => Setting::getValue('ecommerce_restaurant_enabled', '1'),
            'ecommerce_tuck_shop_name' => Setting::getValue('ecommerce_tuck_shop_name', 'Tuck Shop'),
            'ecommerce_restaurant_name' => Setting::getValue('ecommerce_restaurant_name', 'Restaurant'),
        ];

        return view('ecommerce.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'ecommerce_store_name' => 'required|string|max:255',
            'ecommerce_store_email' => 'required|email|max:255',
            'ecommerce_shipping_fee' => 'required|numeric|min:0',
            'ecommerce_free_shipping_limit' => 'required|numeric|min:0',
            'ecommerce_tax_rate' => 'required|numeric|min:0|max:100',
            'ecommerce_store_status' => 'required|in:open,closed',
            'ecommerce_tuck_shop_enabled' => 'nullable|boolean',
            'ecommerce_restaurant_enabled' => 'nullable|boolean',
            'ecommerce_tuck_shop_name' => 'nullable|string|max:120',
            'ecommerce_restaurant_name' => 'nullable|string|max:120',
        ]);

        Setting::setValue('ecommerce_store_name', $request->ecommerce_store_name);
        Setting::setValue('ecommerce_store_email', $request->ecommerce_store_email);
        Setting::setValue('ecommerce_shipping_fee', number_format((float)$request->ecommerce_shipping_fee, 2, '.', ''));
        Setting::setValue('ecommerce_free_shipping_limit', number_format((float)$request->ecommerce_free_shipping_limit, 2, '.', ''));
        Setting::setValue('ecommerce_tax_rate', $request->ecommerce_tax_rate);
        Setting::setValue('ecommerce_store_status', $request->ecommerce_store_status);
        Setting::setValue('ecommerce_tuck_shop_enabled', $request->exists('ecommerce_tuck_shop_enabled') ? ($request->boolean('ecommerce_tuck_shop_enabled') ? '1' : '0') : '1');
        Setting::setValue('ecommerce_restaurant_enabled', $request->exists('ecommerce_restaurant_enabled') ? ($request->boolean('ecommerce_restaurant_enabled') ? '1' : '0') : '1');
        Setting::setValue('ecommerce_tuck_shop_name', $request->filled('ecommerce_tuck_shop_name') ? $request->ecommerce_tuck_shop_name : Setting::getValue('ecommerce_tuck_shop_name', 'Tuck Shop'));
        Setting::setValue('ecommerce_restaurant_name', $request->filled('ecommerce_restaurant_name') ? $request->ecommerce_restaurant_name : Setting::getValue('ecommerce_restaurant_name', 'Restaurant'));

        return redirect()->back()->with('success', 'E-commerce store settings updated successfully!');
    }
}
