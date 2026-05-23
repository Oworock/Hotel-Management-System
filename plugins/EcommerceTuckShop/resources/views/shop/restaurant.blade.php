@extends('layouts.frontend')

@section('title', 'Restaurant Menu')

@section('styles')
<style>
    /* Premium Grid & Columns */
    .shop-layout {
        display: grid;
        grid-template-columns: 2.7fr 1.3fr;
        gap: 2rem;
        align-items: start;
        margin-top: 1rem;
    }

    /* Product Card Sizing and Transitions */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.75rem;
    }

    .product-card {
        border-radius: var(--radius-md);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        background: var(--surface-glass);
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
    }

    .product-card:hover {
        transform: translateY(-6px);
        border-color: var(--primary-light);
        box-shadow: var(--shadow-md);
    }

    .product-card-image-wrapper {
        height: 200px;
        width: 100%;
        overflow: hidden;
        background: var(--background);
        position: relative;
    }

    .product-card-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .product-card:hover .product-card-image {
        transform: scale(1.06);
    }

    .product-card-category {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        background: rgba(0, 0, 0, 0.55);
        backdrop-filter: blur(8px);
        padding: 0.25rem 0.75rem;
        border-radius: var(--radius-full);
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--secondary-light);
        border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .product-card-content {
        padding: 1.25rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .product-card-title {
        font-family: 'Outfit', sans-serif;
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: var(--text-primary);
        line-height: 1.3;
    }

    .product-card-description {
        color: var(--text-secondary);
        font-size: 0.875rem;
        line-height: 1.5;
        margin-bottom: 1.25rem;
        flex-grow: 1;
    }

    .product-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
        border-top: 1px solid var(--border-color);
        padding-top: 1rem;
    }

    /* Custom Quantity Controller */
    .qty-controller {
        display: inline-flex;
        align-items: center;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-full);
        background: var(--surface);
        padding: 0.2rem;
        box-shadow: var(--shadow-sm);
    }

    .qty-btn {
        border: none;
        background: transparent;
        color: var(--text-primary);
        width: 28px;
        height: 28px;
        border-radius: var(--radius-full);
        cursor: pointer;
        font-size: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        font-weight: 700;
    }

    .qty-btn:hover {
        background: var(--primary-glow);
        color: var(--primary);
    }

    .qty-input {
        width: 32px;
        border: none;
        background: transparent;
        text-align: center;
        font-weight: 700;
        font-size: 0.9rem;
        color: var(--text-primary);
        outline: none;
        -moz-appearance: textfield;
    }

    .qty-input::-webkit-outer-spin-button,
    .qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Elegant CTA Button */
    .add-btn {
        background: var(--primary);
        color: #fff;
        border: none;
        border-radius: var(--radius-full);
        padding: 0.5rem 1rem;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.2s ease;
        box-shadow: var(--shadow-sm);
    }

    .add-btn:hover {
        background: var(--primary-light);
        transform: scale(1.03);
    }

    /* Mobile Floating Cart Summary Button */
    .mobile-cart-toggle {
        display: none;
        position: fixed;
        bottom: 24px;
        right: 24px;
        left: 24px;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        color: #fff;
        border-radius: var(--radius-full);
        padding: 1rem 1.5rem;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
        z-index: 1000;
        font-weight: 700;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .mobile-cart-toggle:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 36px rgba(0, 0, 0, 0.3);
    }

    /* Drawer Cart Overlay & Body */
    .cart-drawer-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 1001;
        transition: opacity 0.3s ease;
    }

    .cart-drawer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: var(--surface);
        border-top-left-radius: var(--radius-lg);
        border-top-right-radius: var(--radius-lg);
        padding: 2rem 1.5rem;
        max-height: 80vh;
        overflow-y: auto;
        z-index: 1002;
        transform: translateY(100%);
        transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.15);
    }

    .cart-drawer.active {
        transform: translateY(0);
    }

    .cart-drawer-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 1rem;
        margin-bottom: 1.5rem;
    }

    /* Responsive adjustments */
    @media (max-width: 992px) {
        .shop-layout {
            grid-template-columns: 1fr;
        }

        .desktop-cart-panel {
            display: none !important;
        }

        .mobile-cart-toggle {
            display: flex !important;
        }
    }
</style>
@endsection

@section('content')
<div class="container" style="max-width: 1200px; margin: 2rem auto; padding: 0 1rem; min-height: 70vh;">
    <!-- Page Header -->
    <div class="glass-panel" style="padding: 2.5rem; margin-bottom: 2rem; border-radius: var(--radius-lg); text-align: center; background: var(--surface-glass); backdrop-filter: blur(12px);">
        <h1 style="font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 2.5rem; margin-bottom: 0.5rem; background: linear-gradient(135deg, var(--primary), var(--secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            {{ \App\Models\Setting::getValue('ecommerce_store_name', 'StayFlow') }} Fine Dining
        </h1>
        <p style="color: var(--text-secondary); max-width: 600px; margin: 0 auto; font-size: 1.1rem;">
            Order exquisite dishes prepared by award-winning chefs. Choose room service delivery or table reservation takeaway.
        </p>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 1.5rem;">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="shop-layout">
        <!-- Product Grid Area -->
        <div>
            @if($products->isEmpty())
                <div class="glass-panel" style="padding: 4rem; text-align: center; border-radius: var(--radius-md);">
                    <i class="fa-solid fa-utensils" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
                    <p style="color: var(--text-secondary); font-size: 1.2rem;">Our kitchen is currently closed or updating its menu.</p>
                </div>
            @else
                <div class="product-grid">
                    @foreach($products as $product)
                        <div class="product-card">
                            <div class="product-card-image-wrapper">
                                @if($product->image_path)
                                    <img src="{{ $product->image_path }}" alt="{{ $product->name }}" class="product-card-image">
                                @else
                                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--primary-glow), var(--secondary-glow));">
                                        <i class="fa-solid fa-plate-wheat" style="font-size: 3rem; color: var(--primary);"></i>
                                    </div>
                                @endif
                                <span class="product-card-category">
                                    {{ $product->category }}
                                </span>
                            </div>
                            <div class="product-card-content">
                                <h3 class="product-card-title">
                                    {{ $product->name }}
                                </h3>
                                <p class="product-card-description">
                                    {{ $product->description }}
                                </p>
                                <div class="product-card-footer">
                                    <span style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary);">
                                        {{ $currency }}{{ number_format($product->price, 2) }}
                                    </span>
                                    
                                    <form action="{{ route('cart.add') }}" method="POST" style="display: flex; gap: 0.5rem; align-items: center; margin: 0;">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <div class="qty-controller">
                                            <button type="button" class="qty-btn" onclick="decrementQty(this)">−</button>
                                            <input type="number" name="quantity" value="1" min="1" readonly class="qty-input">
                                            <button type="button" class="qty-btn" onclick="incrementQty(this)">+</button>
                                        </div>
                                        <button type="submit" class="add-btn">
                                            <i class="fa-solid fa-cart-plus"></i> Add
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Sidebar Cart Panel (Desktop) -->
        <div class="glass-panel desktop-cart-panel" style="padding: 1.5rem; border-radius: var(--radius-md); background: var(--surface-glass); border: 1px solid var(--border-color); position: sticky; top: 100px;">
            <h2 style="font-family: 'Outfit', sans-serif; font-size: 1.35rem; font-weight: 800; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem; color: var(--text-primary); border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
                <i class="fa-solid fa-shopping-cart" style="color: var(--primary);"></i> Ordering Cart
            </h2>

            @if(empty($cart))
                <div style="text-align: center; padding: 2rem 0; color: var(--text-muted);">
                    <i class="fa-solid fa-basket-shopping" style="font-size: 2.5rem; margin-bottom: 0.75rem;"></i>
                    <p style="font-size: 0.95rem;">Your cart is empty.</p>
                </div>
            @else
                <div style="max-height: 350px; overflow-y: auto; margin-bottom: 1.25rem; padding-right: 0.25rem;">
                    @php $subtotal = 0; @endphp
                    @foreach($cart as $id => $item)
                        @php $subtotal += $item['price'] * $item['quantity']; @endphp
                        <div style="display: flex; gap: 0.75rem; margin-bottom: 1rem; border-bottom: 1px dashed var(--border-color); padding-bottom: 0.75rem; align-items: center;">
                            <div style="flex-grow: 1;">
                                <h4 style="font-size: 0.9rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem;">
                                    {{ $item['name'] }}
                                </h4>
                                <span style="font-size: 0.8rem; color: var(--text-secondary);">
                                    {{ $item['quantity'] }} × {{ $currency }}{{ number_format($item['price'], 2) }}
                                </span>
                            </div>
                            <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 0.25rem;">
                                <span style="font-size: 0.9rem; font-weight: 700; color: var(--text-primary);">
                                    {{ $currency }}{{ number_format($item['price'] * $item['quantity'], 2) }}
                                </span>
                                <form action="{{ route('cart.remove') }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $id }}">
                                    <button type="submit" style="background: none; border: none; color: #ef4444; font-size: 0.8rem; cursor: pointer; padding: 0;" title="Remove item">
                                        <i class="fa-solid fa-trash-can"></i> Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div style="border-top: 1px solid var(--border-color); padding-top: 1rem; margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.95rem;">
                        <span style="color: var(--text-secondary);">Subtotal:</span>
                        <span style="font-weight: 700; color: var(--text-primary);">{{ $currency }}{{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 1.15rem; font-weight: 800; border-top: 2px solid var(--border-color); padding-top: 0.75rem;">
                        <span style="color: var(--text-primary);">Estimated Total:</span>
                        <span style="color: var(--primary);">{{ $currency }}{{ number_format($subtotal, 2) }}</span>
                    </div>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">* Taxes & delivery calculated at checkout.</p>
                </div>

                <a href="{{ route('checkout') }}" class="btn btn-primary btn-block" style="text-align: center; justify-content: center; text-decoration: none; padding: 0.75rem 1rem; font-weight: 700;">
                    <i class="fa-solid fa-credit-card"></i> Proceed to Checkout
                </a>
            @endif
        </div>
    </div>
</div>

<!-- Mobile Floating Cart Toggle Button -->
@if(!empty($cart))
    @php
        $cartCount = 0;
        $cartTotal = 0;
        foreach($cart as $item) {
            $cartCount += $item['quantity'];
            $cartTotal += $item['price'] * $item['quantity'];
        }
    @endphp
    <div class="mobile-cart-toggle" onclick="toggleCartDrawer()">
        <span><i class="fa-solid fa-shopping-basket" style="margin-right: 0.5rem;"></i> View Cart ({{ $cartCount }})</span>
        <span style="font-weight: 800;">{{ $currency }}{{ number_format($cartTotal, 2) }} <i class="fa-solid fa-arrow-right" style="margin-left: 0.5rem;"></i></span>
    </div>
@endif

<!-- Mobile Cart Drawer View -->
<div class="cart-drawer-overlay" id="cartOverlay" onclick="toggleCartDrawer()"></div>
<div class="cart-drawer" id="cartDrawer">
    <div class="cart-drawer-header">
        <h3 style="font-family: 'Outfit', sans-serif; font-size: 1.35rem; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 0.5rem; color: var(--text-primary);">
            <i class="fa-solid fa-shopping-cart" style="color: var(--primary);"></i> Your Cart
        </h3>
        <button onclick="toggleCartDrawer()" style="background: none; border: none; font-size: 1.5rem; color: var(--text-secondary); cursor: pointer; display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 50%; background: var(--border-color); font-weight: 400;">&times;</button>
    </div>
    
    @if(empty($cart))
        <div style="text-align: center; padding: 2rem 0; color: var(--text-muted);">
            <i class="fa-solid fa-basket-shopping" style="font-size: 2.5rem; margin-bottom: 0.75rem;"></i>
            <p style="font-size: 0.95rem;">Your cart is empty.</p>
        </div>
    @else
        <div style="max-height: 45vh; overflow-y: auto; margin-bottom: 1.5rem; padding-right: 0.25rem;">
            @foreach($cart as $id => $item)
                <div style="display: flex; gap: 0.75rem; margin-bottom: 1rem; border-bottom: 1px dashed var(--border-color); padding-bottom: 0.75rem; align-items: center;">
                    <div style="flex-grow: 1;">
                        <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem;">
                            {{ $item['name'] }}
                        </h4>
                        <span style="font-size: 0.8rem; color: var(--text-secondary);">
                            {{ $item['quantity'] }} × {{ $currency }}{{ number_format($item['price'], 2) }}
                        </span>
                    </div>
                    <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 0.25rem;">
                        <span style="font-size: 0.95rem; font-weight: 700; color: var(--text-primary);">
                            {{ $currency }}{{ number_format($item['price'] * $item['quantity'], 2) }}
                        </span>
                        <form action="{{ route('cart.remove') }}" method="POST" style="margin: 0;">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $id }}">
                            <button type="submit" style="background: none; border: none; color: #ef4444; font-size: 0.8rem; cursor: pointer; padding: 0;" title="Remove item">
                                <i class="fa-solid fa-trash-can"></i> Remove
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="border-top: 1px solid var(--border-color); padding-top: 1rem; margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.95rem;">
                <span style="color: var(--text-secondary);">Subtotal:</span>
                <span style="font-weight: 700; color: var(--text-primary);">{{ $currency }}{{ number_format($cartTotal, 2) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 1.15rem; font-weight: 800; border-top: 2px solid var(--border-color); padding-top: 0.75rem;">
                <span style="color: var(--text-primary);">Estimated Total:</span>
                <span style="color: var(--primary);">{{ $currency }}{{ number_format($cartTotal, 2) }}</span>
            </div>
            <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">* Taxes & delivery calculated at checkout.</p>
        </div>

        <a href="{{ route('checkout') }}" class="btn btn-primary btn-block" style="text-align: center; justify-content: center; text-decoration: none; padding: 0.85rem 1rem; font-weight: 700;">
            <i class="fa-solid fa-credit-card"></i> Proceed to Checkout
        </a>
    @endif
</div>
@endsection

@section('scripts')
<script>
    function decrementQty(btn) {
        const input = btn.parentNode.querySelector('.qty-input');
        let val = parseInt(input.value) || 1;
        if (val > 1) {
            input.value = val - 1;
        }
    }

    function incrementQty(btn) {
        const input = btn.parentNode.querySelector('.qty-input');
        let val = parseInt(input.value) || 1;
        input.value = val + 1;
    }

    function toggleCartDrawer() {
        const overlay = document.getElementById('cartOverlay');
        const drawer = document.getElementById('cartDrawer');
        if (drawer.classList.contains('active')) {
            drawer.classList.remove('active');
            setTimeout(() => overlay.style.display = 'none', 300);
        } else {
            overlay.style.display = 'block';
            setTimeout(() => drawer.classList.add('active'), 10);
        }
    }
</script>
@endsection
