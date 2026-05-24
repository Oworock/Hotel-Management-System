@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Amenity Management</h1>
            <button onclick="openModal()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                + Add Amenity
            </button>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach(collect([['category' => 'room', 'icon' => '🛏️'], ['category' => 'hotel', 'icon' => '🏨'], ['category' => 'dining', 'icon' => '🍽️'], ['category' => 'activity', 'icon' => '🎮'], ['category' => 'service', 'icon' => '🔔']]) as $cat)
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-3 capitalize">{{ $cat['icon'] }} {{ $cat['category'] }} Amenities</h3>
                <div class="space-y-2">
                    @forelse(collect($amenities)->where('category', $cat['category']) as $amenity)
                    <div class="bg-white rounded-lg p-3 shadow hover:shadow-md transition flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $amenity->icon }} {{ $amenity->name }}</p>
                            <p class="text-xs text-gray-600">{{ Str::limit($amenity->description, 50) }}</p>
                        </div>
                        <button onclick="deleteAmenity({{ $amenity->id }})" class="text-red-600 hover:text-red-800">×</button>
                    </div>
                    @empty
                    <div class="bg-gray-100 rounded-lg p-3 text-gray-500 text-sm">No amenities</div>
                    @endforelse
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div id="amenityModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Add Amenity</h2>
        <form method="POST" action="{{ route('super_admin.amenities.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
                <input type="text" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Icon (emoji)</label>
                <input type="text" name="icon" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none" maxlength="2">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
                <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none" required>
                    <option value="room">Room</option>
                    <option value="hotel">Hotel</option>
                    <option value="dining">Dining</option>
                    <option value="activity">Activity</option>
                    <option value="service">Service</option>
                </select>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-semibold">Save</button>
                <button type="button" onclick="closeModal()" class="flex-1 bg-gray-300 text-gray-800 py-2 rounded-lg hover:bg-gray-400 transition">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() { document.getElementById('amenityModal').classList.remove('hidden'); }
function closeModal() { document.getElementById('amenityModal').classList.add('hidden'); }
function deleteAmenity(id) {
    if(confirm('Delete this amenity?')) {
        document.location.href = `/super-admin/amenities/${id}`;
    }
}
</script>
@endsection
