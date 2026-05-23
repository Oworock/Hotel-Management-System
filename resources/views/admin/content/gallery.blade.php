@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Manage Gallery</h1>
            <button onclick="openAddModal()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                + Add Photo
            </button>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
        @endif

        <div class="bg-white rounded-lg shadow p-6">
            @if($photos->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($photos as $photo)
                <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition">
                    <img src="{{ asset('storage/' . $photo->image) }}" alt="{{ $photo->title }}" class="w-full h-40 object-cover">
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-800 mb-1">{{ $photo->title }}</h3>
                        @if($photo->description)
                        <p class="text-sm text-gray-600 mb-2">{{ Str::limit($photo->description, 50) }}</p>
                        @endif
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Order: {{ $photo->order }}</span>
                            <div class="flex gap-2">
                                <button onclick="editPhoto({{ $photo->id }})" class="text-blue-600 hover:text-blue-800">Edit</button>
                                <form method="POST" action="{{ route('admin.gallery.delete', $photo) }}" class="inline" onsubmit="return confirm('Delete this photo?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="p-12 text-center">
                <p class="text-gray-500">No photos yet. Upload your first photo!</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="galleryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-lg shadow-lg max-w-lg w-full mx-4 my-8 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Add/Edit Photo</h2>
        <form id="galleryForm" method="POST" action="" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Title</label>
                <input type="text" name="title" id="title" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Photo</label>
                <input type="file" name="image" id="image" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Order</label>
                <input type="number" name="order" id="order" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" min="0" value="0">
            </div>
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" class="w-4 h-4 text-blue-600 border-gray-300 rounded" checked>
                    <span class="ml-2 text-gray-700">Active</span>
                </label>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">Save</button>
                <button type="button" onclick="closeModal()" class="flex-1 bg-gray-300 text-gray-800 py-2 rounded-lg hover:bg-gray-400 transition">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('galleryForm').reset();
    document.getElementById('galleryForm').action = "{{ route('admin.gallery.store') }}";
    document.getElementById('galleryModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('galleryModal').classList.add('hidden');
}

function editPhoto(id) {
    alert('Edit functionality - implement with AJAX');
}
</script>
@endsection
