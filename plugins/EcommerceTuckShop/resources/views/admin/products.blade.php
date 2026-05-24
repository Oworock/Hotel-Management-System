@extends('layouts.app')

@section('title', 'Tuck Shop & Restaurant Products')

@section('content')
@php
    $currency = \App\Models\Setting::getValue('currency', '$');
    $restrictedType = $restrictedType ?? null;
    $currentType = $currentType ?? request('type', 'all');
    $user = auth()->user();
    if (!$restrictedType && !$user->isAdmin() && !$user->isSuperAdmin()) {
        if ($user->role === 'tuck_shop_manager' || ($user->hasFunction('manage_tuck_shop') && !$user->hasFunction('manage_restaurant'))) {
            $restrictedType = 'tuck_shop';
        } elseif ($user->role === 'restaurant_manager' || ($user->hasFunction('manage_restaurant') && !$user->hasFunction('manage_tuck_shop'))) {
            $restrictedType = 'restaurant';
        }
    }
@endphp
<div class="animate-fade-in" style="display: flex; flex-direction: column; gap: 2rem;">
    <!-- Header with Settings Tabs -->
    <div class="glass-panel" style="padding: 1.5rem 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
            <div>
                <h2 style="font-size: 1.5rem; margin-bottom: 0.25rem;"><i class="fa-solid fa-store" style="color: var(--primary); margin-right: 0.5rem;"></i> E-commerce Products</h2>
                <p style="color: var(--text-secondary); font-size: 0.875rem;">Manage products sold in the hotel tuck shop and dishes offered in the restaurant menu.</p>
            </div>
            <div>
                <button onclick="toggleCreateModal(true)" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-plus"></i> Add Product
                </button>
            </div>
        </div>

        <div style="display: flex; gap: 1rem; border-bottom: 1px solid var(--border-color); overflow-x: auto; white-space: nowrap;">
            @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin() || auth()->user()->hasFunction('manage_settings'))
                <a href="{{ route('admin.settings') }}" class="setting-tab-btn" style="text-decoration: none; padding: 0.75rem 1rem; font-family: inherit; font-size: 0.95rem; font-weight: 600; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent; display: inline-block;">
                    System Settings
                </a>
            @endif
            <a href="{{ route('admin.products') }}" class="setting-tab-btn active-tab" style="text-decoration: none; padding: 0.75rem 1rem; font-family: inherit; font-size: 0.95rem; font-weight: 600; cursor: pointer; color: var(--primary); border-bottom: 2px solid var(--primary); display: inline-block;">
                Shop & Restaurant Products
            </a>
            <a href="{{ route('admin.orders') }}" class="setting-tab-btn" style="text-decoration: none; padding: 0.75rem 1rem; font-family: inherit; font-size: 0.95rem; font-weight: 600; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent; display: inline-block;">
                E-commerce Orders
            </a>
        </div>
    </div>

    @if(!$restrictedType)
    <!-- Product Sub-Filters -->
    <div class="glass-panel" style="padding: 1rem 1.5rem; display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
        <span style="font-weight: 600; font-size: 0.9rem; color: var(--text-secondary);">Catalog:</span>
        <a href="{{ route('admin.products') }}" class="btn btn-sm {{ $currentType === 'all' ? 'btn-primary' : 'btn-outline' }}" style="padding: 0.4rem 1rem; font-size: 0.85rem; text-decoration:none;">All Items</a>
        <a href="{{ route('admin.products', ['type' => 'tuck_shop']) }}" class="btn btn-sm {{ $currentType === 'tuck_shop' ? 'btn-primary' : 'btn-outline' }}" style="padding: 0.4rem 1rem; font-size: 0.85rem; text-decoration:none;">Tuck Shop</a>
        <a href="{{ route('admin.products', ['type' => 'restaurant']) }}" class="btn btn-sm {{ $currentType === 'restaurant' ? 'btn-primary' : 'btn-outline' }}" style="padding: 0.4rem 1rem; font-size: 0.85rem; text-decoration:none;">Restaurant Menu</a>
    </div>
    @endif

    <!-- Products Table / List -->
    <div class="glass-panel" style="padding: 0;">
        <div class="table-container" style="border: none; border-radius: var(--radius-md); overflow: hidden;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product Details</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr class="product-row" data-type="{{ $product->type }}">
                            <td>
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <div style="width: 50px; height: 50px; border-radius: 8px; overflow: hidden; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; border: 1px solid var(--border-color);">
                                        @if($product->image_path)
                                            <img src="{{ $product->image_path }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            <i class="fa-solid fa-image" style="color: var(--text-secondary); opacity: 0.5;"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div style="font-weight: 600;">{{ $product->name }}</div>
                                        <div style="font-size: 0.8rem; color: var(--text-secondary); max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $product->description ?? 'No description provided' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-{{ $product->type === 'tuck_shop' ? 'primary' : 'success' }}">
                                    {{ $product->type === 'tuck_shop' ? 'Tuck Shop' : 'Restaurant' }}
                                </span>
                            </td>
                            <td style="font-size: 0.9rem;">{{ $product->category }}</td>
                            <td style="font-weight: 700; font-family: monospace;">
                                {{ $currency }}{{ number_format($product->price, 2) }}
                            </td>
                            <td>
                                <span class="badge badge-{{ $product->is_available ? 'success' : 'danger' }}">
                                    {{ $product->is_available ? 'Available' : 'Out of Stock' }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 0.5rem; justify-content: flex-end;">
                                    <button onclick="openEditModal({{ json_encode($product) }})" class="btn btn-sm btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </button>
                                    <form action="{{ route('admin.products.delete', $product->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?')" style="margin: 0;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">
                                            <i class="fa-solid fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 4rem 0;">
                                <i class="fa-solid fa-box-open" style="font-size: 3rem; margin-bottom: 1rem; color: var(--border-color); display: block;"></i>
                                <p>No products registered yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add Product -->
<div id="create-modal" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.5); z-index: 10000; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
    <div class="glass-panel" style="width: 100%; max-width: 500px; padding: 2rem; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.25rem;"><i class="fa-solid fa-plus-circle" style="color: var(--primary); margin-right: 0.5rem;"></i> Add New Product</h3>
            <button onclick="toggleCreateModal(false)" style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: var(--text-secondary);"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form action="{{ route('admin.products.store') }}" method="POST">
            @csrf
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Product Name</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Club Sandwich, Soft Drink" required>
            </div>

            @if($restrictedType)
                <input type="hidden" name="type" value="{{ $restrictedType }}">
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label">Product Type</label>
                    <input type="text" class="form-control" value="{{ $restrictedType === 'tuck_shop' ? 'Tuck Shop Item' : 'Restaurant Dish' }}" disabled style="background: rgba(255,255,255,0.05); color: var(--text-secondary);">
                </div>
            @else
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label">Product Type</label>
                    <select name="type" class="form-control form-select" required>
                        <option value="tuck_shop">Tuck Shop Item</option>
                        <option value="restaurant">Restaurant Dish</option>
                    </select>
                </div>
            @endif

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Category</label>
                <input type="text" name="category" class="form-control" placeholder="e.g. Beverage, Fast Food, Snacks" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label">Price ({{ $currency }})</label>
                    <input type="number" name="price" step="0.01" min="0" class="form-control" required placeholder="0.00">
                </div>
                <div class="form-group">
                    <label class="form-label">Availability</label>
                    <div style="display: flex; align-items: center; height: 38px;">
                        <label style="display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="is_available" value="1" checked style="width: 1.2rem; height: 1.2rem; accent-color: var(--primary);">
                            <span>In Stock / Available</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Image Path / URL</label>
                <input type="text" name="image_path" class="form-control" placeholder="https://example.com/image.jpg">
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Brief description of the product or dish"></textarea>
            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                <button type="button" onclick="toggleCreateModal(false)" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Product</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Product -->
<div id="edit-modal" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.5); z-index: 10000; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
    <div class="glass-panel" style="width: 100%; max-width: 500px; padding: 2rem; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.25rem;"><i class="fa-solid fa-pen-to-square" style="color: var(--primary); margin-right: 0.5rem;"></i> Edit Product</h3>
            <button onclick="toggleEditModal(false)" style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: var(--text-secondary);"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form id="edit-form" method="POST">
            @csrf
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Product Name</label>
                <input type="text" name="name" id="edit-name" class="form-control" required>
            </div>

            @if($restrictedType)
                <input type="hidden" name="type" value="{{ $restrictedType }}">
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label">Product Type</label>
                    <input type="text" class="form-control" value="{{ $restrictedType === 'tuck_shop' ? 'Tuck Shop Item' : 'Restaurant Dish' }}" disabled style="background: rgba(255,255,255,0.05); color: var(--text-secondary);">
                </div>
            @else
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label">Product Type</label>
                    <select name="type" id="edit-type" class="form-control form-select" required>
                        <option value="tuck_shop">Tuck Shop Item</option>
                        <option value="restaurant">Restaurant Dish</option>
                    </select>
                </div>
            @endif

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Category</label>
                <input type="text" name="category" id="edit-category" class="form-control" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label">Price ({{ $currency }})</label>
                    <input type="number" name="price" id="edit-price" step="0.01" min="0" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Availability</label>
                    <div style="display: flex; align-items: center; height: 38px;">
                        <label style="display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="is_available" id="edit-available" value="1" style="width: 1.2rem; height: 1.2rem; accent-color: var(--primary);">
                            <span>In Stock / Available</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Image Path / URL</label>
                <input type="text" name="image_path" id="edit-image_path" class="form-control">
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">Description</label>
                <textarea name="description" id="edit-description" class="form-control" rows="3"></textarea>
            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                <button type="button" onclick="toggleEditModal(false)" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleCreateModal(show) {
        document.getElementById('create-modal').style.display = show ? 'flex' : 'none';
    }

    function toggleEditModal(show) {
        document.getElementById('edit-modal').style.display = show ? 'flex' : 'none';
    }

    function openEditModal(product) {
        document.getElementById('edit-name').value = product.name;
        if (document.getElementById('edit-type')) {
            document.getElementById('edit-type').value = product.type;
        }
        document.getElementById('edit-category').value = product.category;
        document.getElementById('edit-price').value = product.price;
        document.getElementById('edit-image_path').value = product.image_path || '';
        document.getElementById('edit-description').value = product.description || '';
        document.getElementById('edit-available').checked = product.is_available ? true : false;
        
        const actionUrl = `/admin/products/${product.id}/update`;
        document.getElementById('edit-form').setAttribute('action', actionUrl);
        
        toggleEditModal(true);
    }

    function filterProducts(type) {
        // Highlight active filter tab
        const buttons = document.querySelectorAll('.filter-tab-btn');
        buttons.forEach(btn => {
            if (btn.id === `filter-btn-${type}`) {
                btn.className = 'btn btn-sm btn-primary filter-tab-btn';
            } else {
                btn.className = 'btn btn-sm btn-outline filter-tab-btn';
            }
        });

        // Show/hide product rows
        const rows = document.querySelectorAll('.product-row');
        rows.forEach(row => {
            if (type === 'all' || row.dataset.type === type) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>

<style>
    .setting-tab-btn {
        transition: border-bottom var(--transition-fast), color var(--transition-fast);
    }
    .setting-tab-btn:hover {
        color: var(--primary) !important;
    }
</style>
@endsection
