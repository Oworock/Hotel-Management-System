@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">{{ isset($item) ? 'Edit' : 'Create' }} Shop Item</h1>
    </div>

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <h3 class="text-red-800 font-semibold mb-2">Please fix the following errors:</h3>
            <ul class="list-disc list-inside text-red-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ isset($item) ? route('admin.ecommerce.shop-items.update', $item) : route('admin.ecommerce.shop-items.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-8">
        @csrf
        @if(isset($item))
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">Item Name *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $item->name ?? '') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter item name">
                @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="category" class="block text-sm font-semibold text-gray-900 mb-2">Category *</label>
                <input type="text" name="category" id="category" value="{{ old('category', $item->category ?? '') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., Electronics, Clothing">
                @error('category')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="sku" class="block text-sm font-semibold text-gray-900 mb-2">SKU</label>
                <input type="text" name="sku" id="sku" value="{{ old('sku', $item->sku ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Stock Keeping Unit">
                @error('sku')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="price" class="block text-sm font-semibold text-gray-900 mb-2">Price *</label>
                <input type="number" name="price" id="price" value="{{ old('price', $item->price ?? '') }}" required step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="0.00">
                @error('price')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="cost_price" class="block text-sm font-semibold text-gray-900 mb-2">Cost Price</label>
                <input type="number" name="cost_price" id="cost_price" value="{{ old('cost_price', $item->cost_price ?? '') }}" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="0.00">
                @error('cost_price')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="stock" class="block text-sm font-semibold text-gray-900 mb-2">Stock Quantity *</label>
                <input type="number" name="stock" id="stock" value="{{ old('stock', $item->stock ?? 0) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('stock')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="reorder_level" class="block text-sm font-semibold text-gray-900 mb-2">Reorder Level</label>
                <input type="number" name="reorder_level" id="reorder_level" value="{{ old('reorder_level', $item->reorder_level ?? 5) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('reorder_level')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2">
                <label for="image" class="block text-sm font-semibold text-gray-900 mb-2">Product Image</label>
                @if(isset($item) && $item->image)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="h-32 w-32 object-cover rounded">
                    </div>
                @endif
                <input type="file" name="image" id="image" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                @error('image')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_featured" value="1" @if(old('is_featured', $item->is_featured ?? false)) checked @endif class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                    <span class="ml-2 text-sm text-gray-900 font-semibold">Featured Item</span>
                </label>
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_available" value="1" @if(old('is_available', $item->is_available ?? true)) checked @endif class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                    <span class="ml-2 text-sm text-gray-900 font-semibold">Available</span>
                </label>
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" @if(old('is_active', $item->is_active ?? true)) checked @endif class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                    <span class="ml-2 text-sm text-gray-900 font-semibold">Active</span>
                </label>
            </div>
        </div>

        <div class="mt-8 flex space-x-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700">
                {{ isset($item) ? 'Update Item' : 'Create Item' }}
            </button>
            <a href="{{ route('admin.ecommerce.shop-items.index') }}" class="bg-gray-300 text-gray-900 px-6 py-2 rounded-lg font-semibold hover:bg-gray-400">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
