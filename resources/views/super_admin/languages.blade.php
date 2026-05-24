@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Language Management</h1>
            <button onclick="openModal()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                + Add Language
            </button>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-lg shadow">
            @if($languages->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Language</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Code</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Default</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($languages as $language)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 text-gray-800 font-medium">{{ $language->flag_emoji }} {{ $language->name }}</td>
                            <td class="px-6 py-3 text-gray-600 font-mono">{{ $language->code }}</td>
                            <td class="px-6 py-3">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $language->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $language->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                @if($language->is_default)
                                <span class="text-blue-600 font-semibold">✓ Default</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-sm space-x-2">
                                <button onclick="editLanguage({{ $language->id }})" class="text-blue-600 hover:text-blue-800 font-semibold">Edit</button>
                                @if(!$language->is_default)
                                <form method="POST" action="{{ route('super_admin.languages.delete', $language) }}" class="inline" onsubmit="return confirm('Delete this language?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">Delete</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="p-12 text-center text-gray-500">No languages configured yet.</div>
            @endif
        </div>
    </div>
</div>

<div id="languageModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Add Language</h2>
        <form method="POST" action="{{ route('super_admin.languages.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Language Name</label>
                <input type="text" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Language Code (e.g., en, es, fr)</label>
                <input type="text" name="code" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Flag Emoji</label>
                <input type="text" name="flag_emoji" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none" placeholder="🇺🇸" maxlength="2">
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-semibold">Save</button>
                <button type="button" onclick="closeModal()" class="flex-1 bg-gray-300 text-gray-800 py-2 rounded-lg hover:bg-gray-400 transition">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('languageModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('languageModal').classList.add('hidden');
}

function editLanguage(id) {
    alert('Edit functionality - implement with AJAX');
}
</script>
@endsection
