@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Special Offers Management</h1>
            <button onclick="openModal()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                + Create Offer
            </button>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-lg shadow">
            @if($offers->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Title</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Type</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Value</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Valid Period</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Usage</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($offers as $offer)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 text-gray-800 font-medium">{{ $offer->title }}</td>
                            <td class="px-6 py-3 text-gray-600 text-sm">{{ str_replace('_', ' ', ucfirst($offer->type)) }}</td>
                            <td class="px-6 py-3 text-gray-800 font-semibold">{{ $offer->value }}{{ in_array($offer->type, ['discount_percent', 'free_nights']) ? (str_contains($offer->type, 'percent') ? '%' : '') : '' }}</td>
                            <td class="px-6 py-3 text-gray-600 text-sm">{{ $offer->valid_from->format('M d') }} - {{ $offer->valid_until->format('M d, Y') }}</td>
                            <td class="px-6 py-3 text-gray-600 text-sm">
                                @if($offer->max_bookings)
                                {{ $offer->used_count }}/{{ $offer->max_bookings }}
                                @else
                                Unlimited
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $offer->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $offer->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-sm space-x-2">
                                <button onclick="editOffer({{ $offer->id }})" class="text-blue-600 hover:text-blue-800">Edit</button>
                                <form method="POST" action="{{ route('super_admin.offers.delete', $offer) }}" class="inline" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="p-12 text-center text-gray-500">No special offers created yet.</div>
            @endif
        </div>
    </div>
</div>

<div id="offerModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-lg shadow-lg max-w-lg w-full mx-4 my-8 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Create Special Offer</h2>
        <form method="POST" action="{{ route('super_admin.offers.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Title</label>
                <input type="text" name="title" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none" placeholder="Early Bird Discount" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none"></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Offer Type</label>
                <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none" required>
                    <option value="discount_percent">Percentage Discount</option>
                    <option value="discount_fixed">Fixed Discount</option>
                    <option value="free_nights">Free Nights</option>
                    <option value="free_upgrade">Free Upgrade</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Value</label>
                <input type="number" name="value" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none" required>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Valid From</label>
                    <input type="date" name="valid_from" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Valid Until</label>
                    <input type="date" name="valid_until" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none" required>
                </div>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Max Bookings (Leave empty for unlimited)</label>
                <input type="number" name="max_bookings" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none">
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-semibold">Create Offer</button>
                <button type="button" onclick="closeModal()" class="flex-1 bg-gray-300 text-gray-800 py-2 rounded-lg hover:bg-gray-400 transition">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() { document.getElementById('offerModal').classList.remove('hidden'); }
function closeModal() { document.getElementById('offerModal').classList.add('hidden'); }
function editOffer(id) { alert('Edit functionality - implement with AJAX'); }
</script>
@endsection
