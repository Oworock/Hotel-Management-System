@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-16">
        <h1 class="text-4xl font-bold text-gray-900 text-center mb-4">Photo Gallery</h1>
        <p class="text-xl text-gray-600 text-center mb-12 max-w-3xl mx-auto">
            Explore our stunning hotel facilities and amenities.
        </p>

        @if($photos->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($photos as $photo)
            @php
                $galleryImage = \Illuminate\Support\Str::startsWith($photo->image, ['http://', 'https://']) ? $photo->image : asset('storage/' . $photo->image);
            @endphp
            <button type="button" onclick="openGalleryLightbox('{{ $galleryImage }}', @js($photo->title), @js($photo->description))" class="group relative overflow-hidden rounded-lg shadow-lg hover:shadow-xl transition-shadow bg-white" style="display:block;text-align:left;border:0;cursor:zoom-in;">
                <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden bg-gray-100">
                    <img src="{{ $galleryImage }}" alt="{{ $photo->title }}" class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-300">
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $photo->title }}</h3>
                    @if($photo->description)
                    <p class="text-gray-600 text-sm">{{ $photo->description }}</p>
                    @endif
                </div>
            </button>
            @endforeach
        </div>
        @else
        <div class="bg-white rounded-lg p-12 text-center shadow-md">
            <p class="text-gray-600 text-lg">No photos available yet.</p>
        </div>
        @endif
    </div>
</div>
<div id="galleryLightbox" style="position:fixed;inset:0;background:rgba(2,6,23,.9);z-index:9999;display:none;align-items:center;justify-content:center;padding:1.5rem;" onclick="closeGalleryLightbox(event)">
    <button type="button" onclick="closeGalleryLightbox(event)" style="position:absolute;top:1rem;right:1rem;width:44px;height:44px;border-radius:999px;border:1px solid rgba(255,255,255,.2);background:rgba(255,255,255,.12);color:#fff;font-size:1.3rem;cursor:pointer;">×</button>
    <figure style="max-width:min(1080px,100%);max-height:92vh;margin:0;">
        <img id="galleryLightboxImage" src="" alt="" style="width:100%;max-height:80vh;object-fit:contain;border-radius:8px;">
        <figcaption style="color:#fff;margin-top:1rem;text-align:center;">
            <strong id="galleryLightboxTitle"></strong>
            <p id="galleryLightboxDescription" style="margin:.5rem 0 0;"></p>
        </figcaption>
    </figure>
</div>
<script>
function openGalleryLightbox(src, title, description) {
    document.getElementById('galleryLightboxImage').src = src;
    document.getElementById('galleryLightboxImage').alt = title || 'Gallery image';
    document.getElementById('galleryLightboxTitle').textContent = title || '';
    document.getElementById('galleryLightboxDescription').textContent = description || '';
    document.getElementById('galleryLightbox').style.display = 'flex';
}

function closeGalleryLightbox(event) {
    if (event.target.id === 'galleryLightbox' || event.target.tagName === 'BUTTON') {
        document.getElementById('galleryLightbox').style.display = 'none';
    }
}
</script>
@endsection
