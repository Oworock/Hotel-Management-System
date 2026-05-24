@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Orders Management</h1>
        <p class="text-gray-600 mt-2">View and manage all shop and restaurant orders</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 flex space-x-4">
        <a href="{{ route('admin.ecommerce.orders.index') }}" class="px-4 py-2 rounded-lg font-semibold @if(!request('type') || request('type') === 'all') bg-blue-600 text-white @else bg-gray-200 text-gray-900 hover:bg-gray-300 @endif">
            All Orders
        </a>
        <a href="{{ route('admin.ecommerce.orders.index', ['type' => 'shop']) }}" class="px-4 py-2 rounded-lg font-semibold @if(request('type') === 'shop') bg-blue-600 text-white @else bg-gray-200 text-gray-900 hover:bg-gray-300 @endif">
            Shop Orders
        </a>
        <a href="{{ route('admin.ecommerce.orders.index', ['type' => 'restaurant']) }}" class="px-4 py-2 rounded-lg font-semibold @if(request('type') === 'restaurant') bg-blue-600 text-white @else bg-gray-200 text-gray-900 hover:bg-gray-300 @endif">
            Restaurant Orders
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left py-3 px-6 font-semibold text-gray-900">Order ID</th>
                        <th class="text-left py-3 px-6 font-semibold text-gray-900">Type</th>
                        <th class="text-left py-3 px-6 font-semibold text-gray-900">Customer</th>
                        <th class="text-left py-3 px-6 font-semibold text-gray-900">Subtotal</th>
                        <th class="text-left py-3 px-6 font-semibold text-gray-900">Tax</th>
                        <th class="text-left py-3 px-6 font-semibold text-gray-900">Total</th>
                        <th class="text-left py-3 px-6 font-semibold text-gray-900">Status</th>
                        <th class="text-left py-3 px-6 font-semibold text-gray-900">Delivery</th>
                        <th class="text-center py-3 px-6 font-semibold text-gray-900">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="py-4 px-6 font-semibold text-gray-900">#{{ $order->id }}</td>
                            <td class="py-4 px-6">
                                <span class="px-3 py-1 rounded-full text-sm font-semibold @if($order->type === 'shop') bg-blue-100 text-blue-800 @else bg-orange-100 text-orange-800 @endif">
                                    {{ ucfirst($order->type) }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $order->user->name ?? 'Guest' }}</p>
                                    @if($order->room_number)
                                        <p class="text-sm text-gray-600">Room {{ $order->room_number }}</p>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-6">${{ number_format($order->subtotal, 2) }}</td>
                            <td class="py-4 px-6">${{ number_format($order->tax, 2) }}</td>
                            <td class="py-4 px-6 font-bold text-gray-900">${{ number_format($order->total, 2) }}</td>
                            <td class="py-4 px-6">
                                <form action="{{ route('admin.ecommerce.orders.update', $order) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="px-3 py-1 rounded text-sm font-semibold @if($order->status === 'completed') bg-green-100 text-green-800 @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800 @elseif($order->status === 'preparing') bg-blue-100 text-blue-800 @else bg-red-100 text-red-800 @endif border-0" onchange="this.form.submit()">
                                        <option value="pending" @selected($order->status === 'pending')>Pending</option>
                                        <option value="preparing" @selected($order->status === 'preparing')>Preparing</option>
                                        <option value="ready" @selected($order->status === 'ready')>Ready</option>
                                        <option value="completed" @selected($order->status === 'completed')>Completed</option>
                                        <option value="cancelled" @selected($order->status === 'cancelled')>Cancelled</option>
                                    </select>
                                </form>
                            </td>
                            <td class="py-4 px-6">
                                <span class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $order->delivery_mode)) }}</span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <a href="{{ route('admin.ecommerce.orders.show', $order) }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="py-8 px-6 text-center text-gray-600" colspan="9">No orders found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($orders instanceof \Illuminate\Pagination\Paginator || $orders instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
