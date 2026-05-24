@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Currency Management</h1>
            <button onclick="openModal()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                + Add Currency
            </button>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($currencies as $currency)
            <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition {{ $currency->is_default ? 'ring-2 ring-blue-400' : '' }}">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">{{ $currency->name }}</h3>
                        <p class="text-gray-600 font-mono">{{ $currency->code }}</p>
                    </div>
                    <span class="text-3xl">{{ $currency->symbol }}</span>
                </div>
                
                <div class="mb-4 pb-4 border-b border-gray-200">
                    <p class="text-sm text-gray-600">Exchange Rate</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $currency->exchange_rate }}</p>
                </div>
                
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm {{ $currency->is_active ? 'text-green-600' : 'text-red-600' }} font-semibold">
                        {{ $currency->is_active ? '✓ Active' : '✗ Inactive' }}
                    </span>
                    @if($currency->is_default)
                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Default</span>
                    @endif
                </div>
                
                <div class="flex gap-2 text-sm">
                    <button onclick="editCurrency({{ $currency->id }})" class="flex-1 text-blue-600 hover:text-blue-800 font-semibold">Edit</button>
                    @if(!$currency->is_default)
                    <form method="POST" action="{{ route('super_admin.currencies.delete', $currency) }}" class="flex-1 inline" onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full text-red-600 hover:text-red-800 font-semibold">Delete</button>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div id="currencyModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Add Currency</h2>
        <form method="POST" action="{{ route('super_admin.currencies.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Currency Name</label>
                <input type="text" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none" placeholder="US Dollar" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Currency Code</label>
                <input type="text" name="code" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none" placeholder="USD" maxlength="3" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Symbol</label>
                <input type="text" name="symbol" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none" placeholder="$" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Exchange Rate</label>
                <input type="number" name="exchange_rate" step="0.000001" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none" value="1" required>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-semibold">Save</button>
                <button type="button" onclick="closeModal()" class="flex-1 bg-gray-300 text-gray-800 py-2 rounded-lg hover:bg-gray-400 transition">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() { document.getElementById('currencyModal').classList.remove('hidden'); }
function closeModal() { document.getElementById('currencyModal').classList.add('hidden'); }
function editCurrency(id) { alert('Edit functionality - implement with AJAX'); }
</script>
@endsection
