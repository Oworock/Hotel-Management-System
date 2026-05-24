@extends('layouts.app')

@section('title', 'E-commerce & Restaurant Orders')

@section('content')
<div class="animate-fade-in" style="display: flex; flex-direction: column; gap: 2rem;">
    @php
        $currentType = $currentType ?? request('type', 'all');
    @endphp
    <!-- Header with Settings Tabs -->
    <div class="glass-panel" style="padding: 1.5rem 2rem;">
        <div>
            <h2 style="font-size: 1.5rem; margin-bottom: 0.25rem;"><i class="fa-solid fa-receipt" style="color: var(--primary); margin-right: 0.5rem;"></i> E-commerce & Room Orders</h2>
            <p style="color: var(--text-secondary); font-size: 0.875rem;">Monitor customer purchases from the tuck shop and room dining orders from the kitchen.</p>
        </div>

        <div style="display: flex; gap: 1rem; border-bottom: 1px solid var(--border-color); margin-top: 1.5rem; overflow-x: auto; white-space: nowrap;">
            @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin() || auth()->user()->hasFunction('manage_settings'))
                <a href="{{ route('admin.settings') }}" class="setting-tab-btn" style="text-decoration: none; padding: 0.75rem 1rem; font-family: inherit; font-size: 0.95rem; font-weight: 600; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent; display: inline-block;">
                    System Settings
                </a>
            @endif
            <a href="{{ route('admin.products') }}" class="setting-tab-btn" style="text-decoration: none; padding: 0.75rem 1rem; font-family: inherit; font-size: 0.95rem; font-weight: 600; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent; display: inline-block;">
                Shop & Restaurant Products
            </a>
            <a href="{{ route('admin.orders') }}" class="setting-tab-btn active-tab" style="text-decoration: none; padding: 0.75rem 1rem; font-family: inherit; font-size: 0.95rem; font-weight: 600; cursor: pointer; color: var(--primary); border-bottom: 2px solid var(--primary); display: inline-block;">
                E-commerce Orders
            </a>
        </div>
    </div>

    <!-- Status Filters -->
    <div class="glass-panel" style="padding: 1rem 1.5rem; display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
        <span style="font-weight: 600; font-size: 0.9rem; color: var(--text-secondary);">Channel:</span>
        <a href="{{ route('admin.orders', request()->except('type')) }}" class="btn btn-sm {{ $currentType === 'all' ? 'btn-primary' : 'btn-outline' }}" style="padding: 0.4rem 1rem; font-size: 0.85rem; text-decoration: none;">All</a>
        <a href="{{ route('admin.orders', array_merge(request()->except('page'), ['type' => 'tuck_shop'])) }}" class="btn btn-sm {{ $currentType === 'tuck_shop' ? 'btn-primary' : 'btn-outline' }}" style="padding: 0.4rem 1rem; font-size: 0.85rem; text-decoration: none;">Tuck Shop</a>
        <a href="{{ route('admin.orders', array_merge(request()->except('page'), ['type' => 'restaurant'])) }}" class="btn btn-sm {{ $currentType === 'restaurant' ? 'btn-primary' : 'btn-outline' }}" style="padding: 0.4rem 1rem; font-size: 0.85rem; text-decoration: none;">Restaurant</a>
        <span style="width:1px;height:28px;background:var(--border-color);"></span>
        <span style="font-weight: 600; font-size: 0.9rem; color: var(--text-secondary);">Filter Status:</span>
        <a href="{{ route('admin.orders') }}" class="btn btn-sm {{ !request()->filled('status') ? 'btn-primary' : 'btn-outline' }}" style="padding: 0.4rem 1rem; font-size: 0.85rem; text-decoration: none;">All Orders</a>
        <a href="{{ route('admin.orders', ['status' => 'pending']) }}" class="btn btn-sm {{ request('status') === 'pending' ? 'btn-primary' : 'btn-outline' }}" style="padding: 0.4rem 1rem; font-size: 0.85rem; text-decoration: none;">Pending</a>
        <a href="{{ route('admin.orders', ['status' => 'preparing']) }}" class="btn btn-sm {{ request('status') === 'preparing' ? 'btn-primary' : 'btn-outline' }}" style="padding: 0.4rem 1rem; font-size: 0.85rem; text-decoration: none;">Preparing</a>
        <a href="{{ route('admin.orders', ['status' => 'delivered']) }}" class="btn btn-sm {{ request('status') === 'delivered' ? 'btn-primary' : 'btn-outline' }}" style="padding: 0.4rem 1rem; font-size: 0.85rem; text-decoration: none;">Delivered</a>
        <a href="{{ route('admin.orders', ['status' => 'cancelled']) }}" class="btn btn-sm {{ request('status') === 'cancelled' ? 'btn-primary' : 'btn-outline' }}" style="padding: 0.4rem 1rem; font-size: 0.85rem; text-decoration: none;">Cancelled</a>
    </div>

    <!-- Orders List -->
    <div class="glass-panel" style="padding: 0;">
        <div class="table-container" style="border: none; border-radius: var(--radius-md); overflow: hidden;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Order Ref</th>
                        <th>Customer Details</th>
                        <th>Delivery Type</th>
                        <th>Items Ordered</th>
                        <th>Total Paid</th>
                        <th>Payment Status</th>
                        <th>Order Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td style="font-weight: 700;">#{{ $order->id }}</td>
                            <td>
                                <div style="font-weight: 600;">{{ $order->customer_name }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-secondary);">
                                    {{ $order->customer_phone }} @if($order->customer_email) | {{ $order->customer_email }} @endif
                                </div>
                            </td>
                            <td>
                                @if($order->delivery_type === 'room')
                                    <span class="badge badge-primary"><i class="fa-solid fa-door-closed"></i> Room {{ $order->delivery_details }}</span>
                                @elseif($order->delivery_type === 'takeaway')
                                    <span class="badge badge-success"><i class="fa-solid fa-bag-shopping"></i> Takeaway</span>
                                @else
                                    <span class="badge badge-outline"><i class="fa-solid fa-map-pin"></i> Address: {{ $order->delivery_details }}</span>
                                @endif
                            </td>
                            <td>
                                <ul style="margin: 0; padding-left: 1.2rem; font-size: 0.85rem; color: var(--text-secondary);">
                                    @foreach($order->orderItems as $item)
                                        <li>
                                            <span style="font-weight: 600; color: var(--text-primary);">{{ $item->quantity }}x</span> 
                                            {{ $item->product->name ?? 'Deleted Product' }} 
                                            <span style="font-family: monospace;">({{ $currency }}{{ number_format($item->price, 2) }})</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td style="font-weight: 700; font-family: monospace;">
                                {{ $currency }}{{ number_format($order->total_price, 2) }}
                            </td>
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 0.2rem;">
                                    <span class="badge badge-{{ $order->payment_status === 'paid' ? 'success' : 'danger' }}" style="width: fit-content;">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                    <span style="font-size: 0.75rem; color: var(--text-secondary);">
                                        Method: {{ $order->payment_method === 'room_charge' ? 'Charge to Room' : ($order->payment_method === 'card' ? 'Card' : 'Cash on Delivery') }}
                                    </span>
                                    @if($order->booking_id)
                                        <span style="font-size: 0.75rem; color: var(--primary);">
                                            Booking: #{{ $order->booking_id }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-{{ $order->status === 'pending' ? 'warning' : ($order->status === 'preparing' ? 'primary' : ($order->status === 'delivered' ? 'success' : 'danger')) }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display:inline-flex;gap:0.5rem;align-items:center;justify-content:flex-end;">
                                    <a href="{{ route('admin.orders.document', $order) }}" target="_blank" class="btn btn-sm btn-outline" style="padding:0.35rem 0.65rem;font-size:0.8rem;text-decoration:none;">
                                        <i class="fa-solid fa-file-invoice"></i> {{ ($order->payment_status === 'paid' || $order->payment_method === 'room_charge' || $order->status === 'delivered') ? 'Receipt' : 'Invoice' }}
                                    </a>
                                    <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" style="margin: 0; display: inline-flex; gap: 0.25rem;">
                                        @csrf
                                        <select name="status" class="form-control form-select" style="padding: 0.35rem 1.5rem 0.35rem 0.75rem; font-size: 0.8rem; width: 120px;" onchange="this.form.submit()">
                                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-secondary); padding: 4rem 0;">
                                <i class="fa-solid fa-receipt" style="font-size: 3rem; margin-bottom: 1rem; color: var(--border-color); display: block;"></i>
                                <p>No orders matching filters found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div style="padding: 1.5rem; display: flex; justify-content: center; border-top: 1px solid var(--border-color);">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>

<style>
    .setting-tab-btn {
        transition: border-bottom var(--transition-fast), color var(--transition-fast);
    }
    .setting-tab-btn:hover {
        color: var(--primary) !important;
    }
</style>
@endsection
