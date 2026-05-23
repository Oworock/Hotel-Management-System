@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Manage Testimonials</h1>
            <button onclick="openAddModal()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                + Add Testimonial
            </button>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
        @endif

        <div class="bg-white rounded-lg shadow">
            @if($testimonials->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
                @foreach($testimonials as $testimonial)
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="font-semibold text-gray-800">{{ $testimonial->guest_name }}</h3>
                            @if($testimonial->guest_title)
                            <p class="text-sm text-gray-600">{{ $testimonial->guest_title }}</p>
                            @endif
                        </div>
                        <span class="flex gap-1">
                            @for($i = 0; $i < $testimonial->rating; $i++)
                            <span class="text-yellow-400">★</span>
                            @endfor
                        </span>
                    </div>
                    <p class="text-gray-700 text-sm mb-3">{{ Str::limit($testimonial->content, 100) }}</p>
                    <div class="flex gap-2 text-sm">
                        <button onclick="editTestimonial({{ $testimonial->id }})" class="text-blue-600 hover:text-blue-800 font-semibold">Edit</button>
                        <form method="POST" action="{{ route('admin.testimonials.delete', $testimonial) }}" class="inline" onsubmit="return confirm('Delete this testimonial?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">Delete</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="p-12 text-center">
                <p class="text-gray-500">No testimonials yet.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="testimonialModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-lg shadow-lg max-w-lg w-full mx-4 my-8 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Add/Edit Testimonial</h2>
        <form id="testimonialForm" method="POST" action="" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Guest Name</label>
                <input type="text" name="guest_name" id="guest_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Title/Position</label>
                <input type="text" name="guest_title" id="guest_title" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Content</label>
                <textarea name="content" id="content" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Rating</label>
                <select name="rating" id="rating" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                    <option value="5" selected>5 Stars</option>
                    <option value="4">4 Stars</option>
                    <option value="3">3 Stars</option>
                    <option value="2">2 Stars</option>
                    <option value="1">1 Star</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Photo</label>
                <input type="file" name="image" id="image" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            </div>
            <div class="mb-6 flex gap-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_featured" id="is_featured" class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                    <span class="ml-2 text-gray-700">Featured</span>
                </label>
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
    document.getElementById('testimonialForm').reset();
    document.getElementById('testimonialForm').action = "{{ route('admin.testimonials.store') }}";
    document.getElementById('testimonialModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('testimonialModal').classList.add('hidden');
}

function editTestimonial(id) {
    alert('Edit functionality - implement with AJAX');
}
</script>
@endsection
