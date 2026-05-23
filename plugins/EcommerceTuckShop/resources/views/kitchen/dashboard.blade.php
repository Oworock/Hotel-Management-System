@extends('layouts.app')

@section('title', 'Kitchen Orders Dashboard')

@section('content')
<div style="padding: 1.5rem 0;">
    <!-- Dashboard Stats Header -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div class="glass-panel" style="padding: 1.5rem; border-radius: var(--radius-md); display: flex; align-items: center; gap: 1rem; border: 1px solid var(--border-color); background: var(--surface-glass);">
            <div style="background: rgba(245, 158, 11, 0.15); color: #f59e0b; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <h3 style="font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); text-transform: uppercase;">Pending Orders</h3>
                <p style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary); margin: 0;">{{ $orders->where('status', 'pending')->count() }}</p>
            </div>
        </div>

        <div class="glass-panel" style="padding: 1.5rem; border-radius: var(--radius-md); display: flex; align-items: center; gap: 1rem; border: 1px solid var(--border-color); background: var(--surface-glass);">
            <div style="background: rgba(59, 130, 246, 0.15); color: #3b82f6; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="fa-solid fa-fire-burner"></i>
            </div>
            <div>
                <h3 style="font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); text-transform: uppercase;">Preparing</h3>
                <p style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary); margin: 0;">{{ $orders->where('status', 'preparing')->count() }}</p>
            </div>
        </div>

        <div class="glass-panel" style="padding: 1.5rem; border-radius: var(--radius-md); display: flex; align-items: center; gap: 1rem; border: 1px solid var(--border-color); background: var(--surface-glass);">
            <div style="background: rgba(16, 185, 129, 0.15); color: #10b981; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <h3 style="font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); text-transform: uppercase;">Total Active</h3>
                <p style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary); margin: 0;">{{ $orders->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Active Orders Section -->
    <h2 style="font-family: 'Outfit', sans-serif; font-size: 1.5rem; font-weight: 800; color: var(--text-primary); margin-bottom: 1.5rem;">
        Active Orders Queue
    </h2>

    @if($orders->isEmpty())
        <div class="glass-panel" style="padding: 4rem 2rem; text-align: center; border-radius: var(--radius-md); border: 1px solid var(--border-color); background: var(--surface-glass);">
            <i class="fa-solid fa-circle-check" style="font-size: 4rem; color: #10b981; margin-bottom: 1.5rem;"></i>
            <h3 style="font-family: 'Outfit', sans-serif; font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">All caught up!</h3>
            <p style="color: var(--text-secondary); max-width: 400px; margin: 0 auto;">No pending or preparing food orders at this time. New orders will appear here automatically.</p>
        </div>
    @else
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;">
            @foreach($orders as $order)
                <div class="glass-panel" style="border-radius: var(--radius-md); border: 1px solid var(--border-color); background: var(--surface-glass); display: flex; flex-direction: column; overflow: hidden; transition: box-shadow 0.3s;">
                    <!-- Card Header -->
                    <div style="padding: 1.25rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: start; background: rgba(0,0,0,0.02);">
                        <div>
                            <span style="font-size: 0.8rem; color: var(--text-secondary); font-weight: 700; text-transform: uppercase;">
                                Order #{{ $order->id }}
                            </span>
                            <h3 style="font-family: 'Outfit', sans-serif; font-size: 1.15rem; font-weight: 700; color: var(--text-primary); margin: 0.25rem 0 0.5rem 0;">
                                {{ $order->customer_name }}
                            </h3>
                            <!-- Delivery details -->
                            <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: var(--text-secondary);">
                                <i class="fa-solid fa-location-dot" style="color: var(--primary);"></i>
                                <strong>{{ ucfirst($order->delivery_type) }}</strong>: {{ $order->delivery_details }}
                            </div>
                        </div>
                        
                        <!-- Status Badge -->
                        @if($order->status === 'pending')
                            <span style="background: rgba(245, 158, 11, 0.15); color: #f59e0b; padding: 0.35rem 0.75rem; border-radius: 50px; font-size: 0.75rem; font-weight: 700; border: 1px solid rgba(245, 158, 11, 0.3);">
                                Pending
                            </span>
                        @else
                            <span style="background: rgba(59, 130, 246, 0.15); color: #3b82f6; padding: 0.35rem 0.75rem; border-radius: 50px; font-size: 0.75rem; font-weight: 700; border: 1px solid rgba(59, 130, 246, 0.3);">
                                Preparing
                            </span>
                        @endif
                    </div>

                    <!-- Order Items -->
                    <div style="padding: 1.25rem; flex-grow: 1;">
                        <span style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; display: block; margin-bottom: 0.75rem;">
                            Items ordered:
                        </span>
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            @foreach($order->orderItems as $item)
                                <li style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; border-bottom: 1px dashed var(--border-color); font-size: 0.95rem;">
                                    <span style="color: var(--text-primary); font-weight: 500;">
                                        {{ $item->product->name ?? 'Unknown Dish' }}
                                        <span style="color: var(--text-muted); font-size: 0.85rem; font-weight: 400; margin-left: 0.5rem;">
                                            ({{ $item->product->type === 'restaurant' ? 'Restaurant' : 'Shop' }})
                                        </span>
                                    </span>
                                    <span style="background: var(--primary-glow); color: var(--primary); padding: 0.2rem 0.6rem; border-radius: var(--radius-sm); font-weight: 700; font-size: 0.85rem;">
                                        Qty: {{ $item->quantity }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                        
                        <!-- Order Timestamp -->
                        <div style="margin-top: 1rem; font-size: 0.8rem; color: var(--text-secondary); display: flex; align-items: center; gap: 0.35rem;">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                            <span>Placed at {{ $order->created_at->format('h:i A') }} ({{ $order->created_at->diffForHumans() }})</span>
                        </div>
                    </div>

                    <!-- Action Footer -->
                    <div style="padding: 1.25rem; border-top: 1px solid var(--border-color); display: flex; gap: 0.5rem; background: rgba(0,0,0,0.01);">
                        @if($order->status === 'pending')
                            <!-- Transition: Pending -> Preparing -->
                            <form action="{{ route('kitchen.orders.status', $order->id) }}" method="POST" style="flex: 1; margin: 0;">
                                @csrf
                                <input type="hidden" name="status" value="preparing">
                                <button type="submit" class="btn btn-primary btn-block" style="justify-content: center; padding: 0.5rem;">
                                    <i class="fa-solid fa-fire-burner"></i> Start Prep
                                </button>
                            </form>
                        @elseif($order->status === 'preparing')
                            <!-- Transition: Preparing -> Delivered -->
                            <form action="{{ route('kitchen.orders.status', $order->id) }}" method="POST" style="flex: 1; margin: 0;">
                                @csrf
                                <input type="hidden" name="status" value="delivered">
                                <button type="submit" class="btn btn-success btn-block" style="justify-content: center; padding: 0.5rem; background: #10b981; color: #fff; border: none;">
                                    <i class="fa-solid fa-circle-check"></i> Complete
                                </button>
                            </form>
                        @endif

                        <!-- Cancel Button -->
                        <form action="{{ route('kitchen.orders.status', $order->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Are you sure you want to cancel this order?')">
                            @csrf
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="btn btn-outline" style="border-color: var(--danger); color: var(--danger); padding: 0.5rem;" title="Cancel Order">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
