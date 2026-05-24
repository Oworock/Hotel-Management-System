@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">{{ isset($item) ? 'Edit' : 'Create' }} Menu Item</h1>
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

    <form action="{{ isset($item) ? route('admin.ecommerce.restaurant-items.update', $item) : route('admin.ecommerce.restaurant-items.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-8">
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
                <input type="text" name="category" id="category" value="{{ old('category', $item->category ?? '') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., Appetizers, Main Course">
                @error('category')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
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
                <label for="calories" class="block text-sm font-semibold text-gray-900 mb-2">Calories</label>
                <input type="number" name="calories" id="calories" value="{{ old('calories', $item->calories ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Calorie count">
                @error('calories')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="preparation_time" class="block text-sm font-semibold text-gray-900 mb-2">Preparation Time (minutes)</label>
                <input type="number" name="preparation_time" id="preparation_time" value="{{ old('preparation_time', $item->preparation_time ?? 15) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('preparation_time')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2">
                <label for="allergens" class="block text-sm font-semibold text-gray-900 mb-2">Allergens</label>
                <input type="text" name="allergens" id="allergens" value="{{ old('allergens', $item->allergens ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., Peanuts, Dairy, Gluten">
                @error('allergens')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2">
                <label for="image" class="block text-sm font-semibold text-gray-900 mb-2">Dish Image</label>
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
                    <input type="checkbox" name="is_vegetarian" value="1" @if(old('is_vegetarian', $item->is_vegetarian ?? false)) checked @endif class="w-4 h-4 text-green-600 border-gray-300 rounded">
                    <span class="ml-2 text-sm text-gray-900 font-semibold">🥬 Vegetarian</span>
                </label>
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_vegan" value="1" @if(old('is_vegan', $item->is_vegan ?? false)) checked @endif class="w-4 h-4 text-green-600 border-gray-300 rounded">
                    <span class="ml-2 text-sm text-gray-900 font-semibold">🌱 Vegan</span>
                </label>
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_spicy" value="1" @if(old('is_spicy', $item->is_spicy ?? false)) checked @endif class="w-4 h-4 text-red-600 border-gray-300 rounded">
                    <span class="ml-2 text-sm text-gray-900 font-semibold">🌶️ Spicy</span>
                </label>
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_featured" value="1" @if(old('is_featured', $item->is_featured ?? false)) checked @endif class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                    <span class="ml-2 text-sm text-gray-900 font-semibold">Featured Dish</span>
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
            <button type="submit" class="bg-orange-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-orange-700">
                {{ isset($item) ? 'Update Item' : 'Create Item' }}
            </button>
            <a href="{{ route('admin.ecommerce.restaurant-items.index') }}" class="bg-gray-300 text-gray-900 px-6 py-2 rounded-lg font-semibold hover:bg-gray-400">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
