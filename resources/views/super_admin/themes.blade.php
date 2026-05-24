@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Theme Management</h1>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($themes as $theme)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition {{ $theme->is_active ? 'ring-4 ring-blue-400' : '' }}">
                <div class="h-40 bg-gradient-to-br" style="background: linear-gradient(135deg, {{ $theme->colors['primary'] ?? '#3B82F6' }} 0%, {{ $theme->colors['secondary'] ?? '#1F2937' }} 100%)"></div>
                
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $theme->name }}</h3>
                    <p class="text-sm text-gray-600 mb-4">{{ $theme->description }}</p>
                    
                    <div class="space-y-3">
                        @if($theme->is_active)
                        <div class="inline-block bg-blue-100 text-blue-800 text-xs px-3 py-1 rounded-full font-semibold">
                            ✓ Active
                        </div>
                        @else
                        <form method="POST" action="{{ route('super_admin.themes.activate', $theme) }}">
                            @csrf
                            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                                Activate Theme
                            </button>
                        </form>
                        @endif
                        
                        <button onclick="openColorModal({{ json_encode($theme) }})" class="w-full bg-gray-200 text-gray-800 py-2 rounded-lg hover:bg-gray-300 transition">
                            Customize Colors
                        </button>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-xs text-gray-500">By {{ $theme->author }} v{{ $theme->version }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Color Customization Modal -->
<div id="colorModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-lg shadow-lg max-w-lg w-full mx-4 my-8 p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Customize Colors</h2>
        <form id="colorForm" method="POST" action="">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Primary Color</label>
                    <input type="color" name="primary" id="primary" class="w-full h-10 border border-gray-300 rounded-lg cursor-pointer">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Secondary Color</label>
                    <input type="color" name="secondary" id="secondary" class="w-full h-10 border border-gray-300 rounded-lg cursor-pointer">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Accent Color</label>
                    <input type="color" name="accent" id="accent" class="w-full h-10 border border-gray-300 rounded-lg cursor-pointer">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Background Color</label>
                    <input type="color" name="background" id="background" class="w-full h-10 border border-gray-300 rounded-lg cursor-pointer">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Text Color</label>
                    <input type="color" name="text" id="text" class="w-full h-10 border border-gray-300 rounded-lg cursor-pointer">
                </div>
            </div>
            
            <div class="flex gap-3 mt-6">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                    Save Colors
                </button>
                <button type="button" onclick="closeColorModal()" class="flex-1 bg-gray-300 text-gray-800 py-2 rounded-lg hover:bg-gray-400 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openColorModal(theme) {
    document.getElementById('colorForm').action = `/super-admin/themes/${theme.id}/colors`;
    document.getElementById('primary').value = theme.colors?.primary || '#3B82F6';
    document.getElementById('secondary').value = theme.colors?.secondary || '#1F2937';
    document.getElementById('accent').value = theme.colors?.accent || '#10B981';
    document.getElementById('background').value = theme.colors?.background || '#F9FAFB';
    document.getElementById('text').value = theme.colors?.text || '#111827';
    document.getElementById('colorModal').classList.remove('hidden');
}

function closeColorModal() {
    document.getElementById('colorModal').classList.add('hidden');
}
</script>
@endsection
