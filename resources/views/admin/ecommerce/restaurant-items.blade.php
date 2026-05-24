@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Restaurant Menu Items</h1>
            <p class="text-gray-600 mt-2">Manage your restaurant menu</p>
        </div>
        <a href="{{ route('admin.ecommerce.restaurant-items.create') }}" class="bg-orange-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-orange-700">
            Add Menu Item
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left py-3 px-6 font-semibold text-gray-900">Name</th>
                        <th class="text-left py-3 px-6 font-semibold text-gray-900">Category</th>
                        <th class="text-left py-3 px-6 font-semibold text-gray-900">Price</th>
                        <th class="text-left py-3 px-6 font-semibold text-gray-900">Dietary</th>
                        <th class="text-left py-3 px-6 font-semibold text-gray-900">Status</th>
                        <th class="text-center py-3 px-6 font-semibold text-gray-900">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="py-4 px-6">
                                <div class="flex items-center">
                                    @if($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="w-10 h-10 rounded mr-3 object-cover">
                                    @endif
                                    <span class="font-semibold text-gray-900">{{ $item->name }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-gray-600">{{ $item->category }}</td>
                            <td class="py-4 px-6 font-semibold text-gray-900">${{ number_format($item->price, 2) }}</td>
                            <td class="py-4 px-6">
                                <div class="space-y-1">
                                    @if($item->is_vegetarian)
                                        <span class="block text-sm text-green-600 font-semibold">🥬 Vegetarian</span>
                                    @endif
                                    @if($item->is_vegan)
                                        <span class="block text-sm text-green-600 font-semibold">🌱 Vegan</span>
                                    @endif
                                    @if($item->is_spicy)
                                        <span class="block text-sm text-red-600 font-semibold">🌶️ Spicy</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-3 py-1 rounded-full text-sm font-semibold @if($item->is_available) bg-green-100 text-green-800 @else bg-gray-100 text-gray-800 @endif">
                                    {{ $item->is_available ? 'Available' : 'Unavailable' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('admin.ecommerce.restaurant-items.edit', $item) }}" class="text-blue-600 hover:text-blue-800 font-semibold">Edit</a>
                                    <form action="{{ route('admin.ecommerce.restaurant-items.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="py-8 px-6 text-center text-gray-600" colspan="6">No items found. <a href="{{ route('admin.ecommerce.restaurant-items.create') }}" class="text-blue-600 hover:text-blue-800 font-semibold">Create one now</a></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($items instanceof \Illuminate\Pagination\Paginator || $items instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-6">
            {{ $items->links() }}
        </div>
    @endif
</div>
@endsection
