@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Rate Plans Management</h1>
            <button onclick="openModal()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                + Create Rate Plan
            </button>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($ratePlans ?? [] as $plan)
            <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">{{ $plan->name }}</h3>
                        <p class="text-gray-600">{{ $plan->roomType->name ?? 'N/A' }}</p>
                    </div>
                    <span class="text-2xl font-bold text-blue-600">${{ number_format($plan->base_price, 2) }}</span>
                </div>
                
                <div class="space-y-2 mb-4 pb-4 border-b border-gray-200">
                    <p class="text-sm text-gray-600"><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $plan->booking_type)) }}</p>
                    <p class="text-sm text-gray-600"><strong>Period:</strong> {{ $plan->start_date?->format('M d, Y') }} - {{ $plan->end_date?->format('M d, Y') }}</p>
                    @if($plan->min_price || $plan->max_price)
                    <p class="text-sm text-gray-600"><strong>Range:</strong> ${{ number_format($plan->min_price, 2) }} - ${{ number_format($plan->max_price, 2) }}</p>
                    @endif
                </div>
                
                <div class="flex gap-2 text-sm">
                    <button onclick="editPlan({{ $plan->id }})" class="flex-1 text-blue-600 hover:text-blue-800 font-semibold">Edit</button>
                    <form method="POST" action="/admin/rate-plans/{{ $plan->id }}" class="flex-1 inline" onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full text-red-600 hover:text-red-800 font-semibold">Delete</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div id="planModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-lg shadow-lg max-w-lg w-full mx-4 my-8 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Create Rate Plan</h2>
        <form method="POST" action="/admin/rate-plans">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Plan Name</label>
                <input type="text" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Base Price</label>
                <input type="number" name="base_price" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none" required>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Min Price</label>
                    <input type="number" name="min_price" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Max Price</label>
                    <input type="number" name="max_price" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none">
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Booking Type</label>
                <select name="booking_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none">
                    <option value="direct">Direct</option>
                    <option value="ota">OTA</option>
                    <option value="all">All</option>
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-semibold">Create</button>
                <button type="button" onclick="closeModal()" class="flex-1 bg-gray-300 text-gray-800 py-2 rounded-lg hover:bg-gray-400 transition">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() { document.getElementById('planModal').classList.remove('hidden'); }
function closeModal() { document.getElementById('planModal').classList.add('hidden'); }
function editPlan(id) { alert('Edit functionality - implement with AJAX'); }
</script>
@endsection
