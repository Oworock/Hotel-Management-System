@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Manage FAQs</h1>
            <button onclick="openAddModal()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                + Add FAQ
            </button>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
        @endif

        <div class="bg-white rounded-lg shadow">
            @if($faqs->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Question</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Order</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($faqs as $faq)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 text-gray-800">{{ $faq->question }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $faq->order }}</td>
                            <td class="px-6 py-3">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $faq->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $faq->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-sm space-x-2">
                                <button onclick="editFaq({{ $faq->id }})" class="text-blue-600 hover:text-blue-800 font-semibold">Edit</button>
                                <form method="POST" action="{{ route('admin.faqs.delete', $faq) }}" class="inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="p-12 text-center">
                <p class="text-gray-500">No FAQs yet. Create your first one!</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="faqModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">{{ isset($faq) ? 'Edit FAQ' : 'Add FAQ' }}</h2>
        <form id="faqForm" method="POST" action="">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Question</label>
                <input type="text" name="question" id="question" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Answer</label>
                <textarea name="answer" id="answer" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required></textarea>
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
                <button type="button" onclick="closeFaqModal()" class="flex-1 bg-gray-300 text-gray-800 py-2 rounded-lg hover:bg-gray-400 transition">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('faqForm').reset();
    document.getElementById('faqForm').action = "{{ route('admin.faqs.store') }}";
    document.getElementById('faqModal').classList.remove('hidden');
}

function closeFaqModal() {
    document.getElementById('faqModal').classList.add('hidden');
}

function editFaq(id) {
    // In a real scenario, you'd fetch the FAQ data and populate the form
    // For now, redirect to edit page or implement via AJAX
    alert('Edit functionality - implement as needed');
}
</script>
@endsection
