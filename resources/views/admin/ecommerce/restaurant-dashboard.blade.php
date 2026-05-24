@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Restaurant Dashboard</h1>
        <p class="text-gray-600 mt-2">Manage your menu and orders</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Menu Items</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalItems }}</p>
                </div>
                <div class="text-4xl text-orange-500">🍽️</div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Unavailable Items</p>
                    <p class="text-3xl font-bold text-orange-600">{{ $unavailableItems }}</p>
                </div>
                <div class="text-4xl text-orange-500">⛔</div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Revenue</p>
                    <p class="text-3xl font-bold text-green-600">${{ number_format($totalRevenue, 2) }}</p>
                </div>
                <div class="text-4xl text-green-500">💰</div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Active Orders</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $activeOrders }}</p>
                </div>
                <div class="text-4xl text-blue-500">📋</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Recent Orders</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-3 px-4 text-gray-600 font-semibold">Order ID</th>
                            <th class="text-left py-3 px-4 text-gray-600 font-semibold">Customer</th>
                            <th class="text-left py-3 px-4 text-gray-600 font-semibold">Total</th>
                            <th class="text-left py-3 px-4 text-gray-600 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-4">#{{ $order->id }}</td>
                                <td class="py-3 px-4">{{ $order->user->name ?? 'Guest' }}</td>
                                <td class="py-3 px-4">${{ number_format($order->total, 2) }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-3 py-1 rounded-full text-sm font-semibold @if($order->status === 'completed') bg-green-100 text-green-800 @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800 @elseif($order->status === 'preparing') bg-blue-100 text-blue-800 @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-3 px-4 text-center text-gray-600" colspan="4">No orders yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Quick Actions</h2>
            <div class="space-y-3">
                <a href="{{ route('admin.ecommerce.restaurant-items.create') }}" class="block w-full bg-orange-600 text-white py-2 px-4 rounded-lg text-center font-semibold hover:bg-orange-700">
                    Add Menu Item
                </a>
                <a href="{{ route('admin.ecommerce.restaurant-items.index') }}" class="block w-full bg-gray-200 text-gray-900 py-2 px-4 rounded-lg text-center font-semibold hover:bg-gray-300">
                    Manage Menu
                </a>
                <a href="{{ route('admin.ecommerce.orders.index') }}" class="block w-full bg-gray-200 text-gray-900 py-2 px-4 rounded-lg text-center font-semibold hover:bg-gray-300">
                    View All Orders
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
