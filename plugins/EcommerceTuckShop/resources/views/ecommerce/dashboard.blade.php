@extends('layouts.app')

@section('title', 'E-commerce Shop Dashboard')

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 1.5rem 2rem 2.5rem 2rem;">
    <!-- Title and Header Actions -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;">
                <i class="fa-solid fa-gauge-high" style="color: var(--primary); margin-right: 0.5rem;"></i> Shop Dashboard
            </h2>
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.25rem;">Overview of tuck shop and restaurant sales activity.</p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('admin.ecommerce.analytics') }}" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-chart-simple"></i> View Analytics
            </a>
            <a href="{{ route('admin.ecommerce.settings') }}" class="btn" style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: #fff; border: none;">
                <i class="fa-solid fa-sliders"></i> Shop Settings
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <!-- Total Revenue -->
        <div class="glass-panel stat-card" style="display: flex; align-items: center; justify-content: space-between; padding: 1.5rem; border-radius: var(--radius-md); box-shadow: var(--shadow-sm);">
            <div>
                <p style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">Total Revenue</p>
                <div class="stat-value" style="color: var(--success); font-size: 1.8rem; font-weight: 700; margin-top: 0.5rem;">
                    {{ $currency === 'USD' ? '$' : $currency }}{{ number_format($totalRevenue, 2) }}
                </div>
            </div>
            <div class="stat-icon" style="font-size: 2rem; color: var(--success); opacity: 0.85;"><i class="fa-solid fa-sack-dollar"></i></div>
        </div>

        <!-- Total Orders -->
        <div class="glass-panel stat-card" style="display: flex; align-items: center; justify-content: space-between; padding: 1.5rem; border-radius: var(--radius-md); box-shadow: var(--shadow-sm);">
            <div>
                <p style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">Total Orders</p>
                <div class="stat-value" style="color: var(--text-primary); font-size: 1.8rem; font-weight: 700; margin-top: 0.5rem;">
                    {{ $totalOrders }}
                </div>
            </div>
            <div class="stat-icon" style="font-size: 2rem; color: var(--primary); opacity: 0.85;"><i class="fa-solid fa-basket-shopping"></i></div>
        </div>

        <!-- Average Order Value -->
        <div class="glass-panel stat-card" style="display: flex; align-items: center; justify-content: space-between; padding: 1.5rem; border-radius: var(--radius-md); box-shadow: var(--shadow-sm);">
            <div>
                <p style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">Avg Order Value</p>
                <div class="stat-value" style="color: var(--primary); font-size: 1.8rem; font-weight: 700; margin-top: 0.5rem;">
                    {{ $currency === 'USD' ? '$' : $currency }}{{ number_format($averageOrderValue, 2) }}
                </div>
            </div>
            <div class="stat-icon" style="font-size: 2rem; color: var(--primary); opacity: 0.85;"><i class="fa-solid fa-file-invoice-dollar"></i></div>
        </div>

        <!-- Pending Orders -->
        <div class="glass-panel stat-card" style="display: flex; align-items: center; justify-content: space-between; padding: 1.5rem; border-radius: var(--radius-md); box-shadow: var(--shadow-sm);">
            <div>
                <p style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">Pending Orders</p>
                <div class="stat-value" style="color: var(--warning); font-size: 1.8rem; font-weight: 700; margin-top: 0.5rem;">
                    {{ $pendingOrdersCount }}
                </div>
            </div>
            <div class="stat-icon" style="font-size: 2rem; color: var(--warning); opacity: 0.85;"><i class="fa-solid fa-clock-rotate-left"></i></div>
        </div>

        <!-- Catalog Status -->
        <div class="glass-panel stat-card" style="display: flex; align-items: center; justify-content: space-between; padding: 1.5rem; border-radius: var(--radius-md); box-shadow: var(--shadow-sm);">
            <div>
                <p style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">Products (Active/Total)</p>
                <div class="stat-value" style="color: var(--text-primary); font-size: 1.8rem; font-weight: 700; margin-top: 0.5rem;">
                    {{ $availableProducts }} / {{ $totalProducts }}
                </div>
            </div>
            <div class="stat-icon" style="font-size: 2rem; color: var(--text-secondary); opacity: 0.85;"><i class="fa-solid fa-cubes"></i></div>
        </div>
    </div>

    <!-- Main Content Breakdown -->
    <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 2rem; margin-top: 2rem; align-items: start;">
        <!-- Recent Orders Panel -->
        <div class="glass-panel" style="padding: 1.5rem; border-radius: var(--radius-md); display: flex; flex-direction: column;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.25rem; font-weight: 600; margin: 0; color: var(--text-primary);">
                    <i class="fa-solid fa-receipt" style="color: var(--primary); margin-right: 0.5rem;"></i> Recent Shop Orders
                </h3>
                <a href="{{ route('admin.orders') }}" style="font-size: 0.85rem; color: var(--primary); font-weight: 600; text-decoration: none;">
                    Manage All <i class="fa-solid fa-arrow-right" style="font-size: 0.75rem;"></i>
                </a>
            </div>

            <div class="table-container" style="border: none;">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Payment</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                            <tr>
                                <td style="font-weight: 600;">#{{ $order->id }}</td>
                                <td>
                                    @if($order->customer)
                                        {{ $order->customer->name }}
                                    @elseif($order->guest_name)
                                        {{ $order->guest_name }} <span class="badge badge-info" style="font-size: 0.65rem; padding: 0.1rem 0.3rem;">Guest</span>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td style="font-size: 0.85rem; color: var(--text-secondary);">
                                    {{ $order->created_at->format('M d, H:i') }}
                                </td>
                                <td>
                                    <span class="badge" style="background: var(--primary-glow); color: var(--primary);">
                                        {{ ucfirst($order->type ?? 'Tuck Shop') }}
                                    </span>
                                </td>
                                <td style="font-size: 0.85rem;">
                                    @switch($order->payment_method)
                                        @case('cod')
                                            COD
                                            @break
                                        @case('card')
                                            Card
                                            @break
                                        @case('room_charge')
                                            Room Charge
                                            @break
                                        @default
                                            {{ strtoupper($order->payment_method) }}
                                    @endswitch
                                    <span style="display: block; font-size: 0.75rem; color: {{ $order->payment_status === 'paid' ? 'var(--success)' : 'var(--warning)' }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </td>
                                <td style="font-weight: 600; color: var(--success);">
                                    {{ $currency === 'USD' ? '$' : $currency }}{{ number_format($order->total_price, 2) }}
                                </td>
                                <td>
                                    @switch($order->status)
                                        @case('pending')
                                            <span class="badge badge-warning">Pending</span>
                                            @break
                                        @case('preparing')
                                            <span class="badge badge-info">Preparing</span>
                                            @break
                                        @case('completed')
                                            <span class="badge badge-success">Completed</span>
                                            @break
                                        @case('delivered')
                                            <span class="badge badge-success" style="opacity: 0.85;">Delivered</span>
                                            @break
                                        @case('cancelled')
                                            <span class="badge badge-danger">Cancelled</span>
                                            @break
                                        @default
                                            <span class="badge">{{ ucfirst($order->status) }}</span>
                                    @endswitch
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 2rem;">
                                    <i class="fa-solid fa-inbox" style="font-size: 2rem; display: block; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                                    No orders found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Controls / Catalog Overview -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Store Status Panel -->
            <div class="glass-panel" style="padding: 1.5rem; border-radius: var(--radius-md);">
                <h3 style="font-size: 1.1rem; font-weight: 600; margin-top: 0; margin-bottom: 1rem; color: var(--text-primary);">
                    <i class="fa-solid fa-shop" style="color: var(--primary); margin-right: 0.5rem;"></i> Store Status
                </h3>
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    @php
                        $storeStatus = \App\Models\Setting::getValue('ecommerce_store_status', 'open');
                    @endphp
                    @if($storeStatus === 'open')
                        <div style="width: 12px; height: 12px; border-radius: 50%; background: var(--success); box-shadow: 0 0 8px var(--success);"></div>
                        <span style="font-weight: 600; color: var(--success);">Open & Accepting Orders</span>
                    @else
                        <div style="width: 12px; height: 12px; border-radius: 50%; background: var(--danger); box-shadow: 0 0 8px var(--danger);"></div>
                        <span style="font-weight: 600; color: var(--danger);">Closed to Customers</span>
                    @endif
                </div>
                <div style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.4; margin-bottom: 1rem;">
                    Store Name: <strong>{{ \App\Models\Setting::getValue('ecommerce_store_name', 'StayFlow Tuck Shop') }}</strong><br>
                    Support Email: <strong>{{ \App\Models\Setting::getValue('ecommerce_store_email', 'shop@stayflow.com') }}</strong><br>
                    Shipping Fee: <strong>{{ $currency === 'USD' ? '$' : $currency }}{{ number_format(\App\Models\Setting::getValue('ecommerce_shipping_fee', 5.00), 2) }}</strong><br>
                    Free Shipping Above: <strong>{{ $currency === 'USD' ? '$' : $currency }}{{ number_format(\App\Models\Setting::getValue('ecommerce_free_shipping_limit', 50.00), 2) }}</strong>
                </div>
                <a href="{{ route('admin.ecommerce.settings') }}" class="btn btn-outline btn-block" style="text-align: center; text-decoration: none; display: block; font-size: 0.85rem;">
                    Configure Store Settings
                </a>
            </div>

            <!-- Shop Inventory Link Panel -->
            <div class="glass-panel" style="padding: 1.5rem; border-radius: var(--radius-md); background: linear-gradient(135deg, var(--primary-glow), transparent);">
                <h3 style="font-size: 1.1rem; font-weight: 600; margin-top: 0; margin-bottom: 0.5rem; color: var(--text-primary);">
                    <i class="fa-solid fa-boxes-stacked" style="color: var(--primary); margin-right: 0.5rem;"></i> Product Catalog
                </h3>
                <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.4; margin-bottom: 1rem;">
                    Manage items, pricing, availability, and description categorization for both tuck shop products and restaurant dishes.
                </p>
                <a href="{{ route('admin.products') }}" class="btn btn-block" style="text-align: center; text-decoration: none; display: block; font-size: 0.85rem; background: var(--primary); color: #fff; border: none;">
                    Go to Products Catalog
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
