@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Analytics & Reports</h1>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <p class="text-gray-600 text-sm mb-2">Total Bookings</p>
                <p class="text-4xl font-bold text-blue-600">{{ \App\Models\Booking::count() }}</p>
                <p class="text-xs text-gray-500 mt-2">This month</p>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-6">
                <p class="text-gray-600 text-sm mb-2">Revenue</p>
                <p class="text-4xl font-bold text-green-600">${{ number_format(\App\Models\Booking::sum('total_price'), 2) }}</p>
                <p class="text-xs text-gray-500 mt-2">Total</p>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-6">
                <p class="text-gray-600 text-sm mb-2">Occupancy Rate</p>
                <p class="text-4xl font-bold text-purple-600">78%</p>
                <p class="text-xs text-gray-500 mt-2">Average</p>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-6">
                <p class="text-gray-600 text-sm mb-2">Guest Reviews</p>
                <p class="text-4xl font-bold text-yellow-600">4.8/5</p>
                <p class="text-xs text-gray-500 mt-2">Average Rating</p>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Booking Trends</h3>
                <div class="h-64 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg flex items-center justify-center">
                    <p class="text-gray-500">Chart visualization would go here</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Room Type Performance</h3>
                <div class="h-64 bg-gradient-to-br from-green-50 to-green-100 rounded-lg flex items-center justify-center">
                    <p class="text-gray-500">Chart visualization would go here</p>
                </div>
            </div>
        </div>

        <!-- Reports Table -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Recent Transactions</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Booking ID</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Guest</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Amount</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse(\App\Models\Booking::latest()->take(5)->get() as $booking)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 text-gray-800 font-mono">#{{ $booking->id }}</td>
                            <td class="px-6 py-3 text-gray-800">{{ $booking->user->name ?? 'N/A' }}</td>
                            <td class="px-6 py-3 text-gray-800 font-semibold">${{ number_format($booking->total_price, 2) }}</td>
                            <td class="px-6 py-3">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold 
                                    {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-gray-600 text-sm">{{ $booking->created_at->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-6 text-center text-gray-500">No transactions yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
