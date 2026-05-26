<style>
.gallery-lightbox {
    position: fixed;
    inset: 0;
    background: rgba(2, 6, 23, 0.88);
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
}

.gallery-lightbox.active {
    display: flex;
}

.gallery-lightbox figure {
    max-width: min(1080px, 100%);
    max-height: 92vh;
    margin: 0;
}

.gallery-lightbox img {
    width: 100%;
    max-height: 80vh;
    object-fit: contain;
    border-radius: 8px;
}

.gallery-lightbox figcaption {
    color: #fff;
    margin-top: 1rem;
    text-align: center;
}

.gallery-lightbox button {
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 44px;
    height: 44px;
    border-radius: 999px;
    border: 1px solid rgba(255, 255, 255, 0.18);
    background: rgba(255, 255, 255, 0.12);
    color: #fff;
    cursor: pointer;
    font-size: 1.3rem;
}

.content-kicker {
    display: inline-flex;
    margin: 0 0 .55rem;
    font-size: .76rem;
    font-weight: 900;
    letter-spacing: .06em;
    text-transform: uppercase;
    opacity: .78;
}

.room-card,
.blog-card,
.testimonial-card,
.gallery-card {
    display: flex;
    flex-direction: column;
}

.room-card-body,
.room-card-footer {
    display: grid;
    gap: .75rem;
}

.room-card-footer {
    margin-top: auto;
    padding-top: 1rem;
}

.faq-list {
    display: grid;
    gap: 1rem;
}

.faq-item summary {
    font-weight: 900;
    cursor: pointer;
    list-style: none;
}

.faq-item summary::-webkit-details-marker {
    display: none;
}

.faq-item p {
    margin-top: 1rem;
    line-height: 1.75;
}

.stars {
    color: #f59e0b;
    letter-spacing: .12em;
    margin-bottom: 1rem;
}

.blog-show-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 320px;
    gap: 2rem;
    align-items: start;
}

.blog-sidebar {
    position: sticky;
    top: 6rem;
}

.article-content {
    line-height: 1.9;
}

.mini-room {
    border-top: 1px solid currentColor;
    padding-top: 1rem;
    margin-top: 1rem;
}

.related-section {
    margin-top: 2.5rem;
}

.content-page {
    max-width: 900px;
    line-height: 1.8;
}

.map-card {
    margin-top: 2rem;
    padding: 0 !important;
    overflow: hidden;
}

.map-card iframe {
    border: 0;
    display: block;
}

.visit-card {
    margin-top: 1rem;
}

.service-number {
    font-weight: 900;
    opacity: .45;
}

@media (max-width: 900px) {
    .blog-show-layout {
        grid-template-columns: 1fr !important;
    }

    .blog-sidebar {
        position: static !important;
    }
}
</style>

@switch($pageType)
    @case('rooms')
        @if(!empty($pageBody))
            <article class="page-card content-page" style="margin-bottom:1.5rem;">
                <p>{{ $pageBody }}</p>
            </article>
        @endif
        <div class="card-grid themed-rooms">
            @forelse($roomTypes as $roomType)
                @php
                    $roomImage = collect($roomType->images ?? [])->first();
                    $roomImageUrl = $roomImage
                        ? (\Illuminate\Support\Str::startsWith($roomImage, ['http://', 'https://', '/']) ? $roomImage : asset('storage/' . $roomImage))
                        : 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=1200&q=80';
                @endphp
                <article class="room-card">
                    <div class="media-block"><img src="{{ $roomImageUrl }}" alt="{{ $roomType->name }}"></div>
                    <div class="room-card-body">
                        <p class="content-kicker">{{ $roomType->available_count }} available</p>
                        <h3>{{ $roomType->name }}</h3>
                        <p>{{ \Illuminate\Support\Str::limit($roomType->description, 140) }}</p>
                    </div>
                    <div class="room-card-footer">
                        <div class="price">{{ $currency }}{{ number_format((float) ($roomType->base_price ?? $roomType->price ?? 0), 0) }}</div>
                        @auth
                            <a class="btn-link" href="{{ route('customer.book', $roomType->id) }}">Book Room</a>
                        @else
                            <a class="btn-link" href="{{ route('customer.book', $roomType->id) }}">Book Room</a>
                        @endauth
                    </div>
                </article>
            @empty
                <div class="page-card">No rooms available at the moment.</div>
            @endforelse
        </div>
        @break

    @case('faqs')
        @if(!empty($pageBody))
            <article class="page-card content-page" style="margin-bottom:1.5rem;">
                <p>{{ $pageBody }}</p>
            </article>
        @endif
        <div class="faq-list">
            @forelse($faqs as $faq)
                <details class="faq-item">
                    <summary>{{ $faq->question }}</summary>
                    <p>{{ $faq->answer }}</p>
                </details>
            @empty
                <div class="page-card">No FAQs available yet.</div>
            @endforelse
        </div>
        @break

    @case('testimonials')
        @if(!empty($pageBody))
            <article class="page-card content-page" style="margin-bottom:1.5rem;">
                <p>{{ $pageBody }}</p>
            </article>
        @endif
        <div class="card-grid testimonial-grid">
            @forelse($testimonials as $testimonial)
                <article class="testimonial-card">
                    <div class="stars">{{ str_repeat('★', (int) ($testimonial->rating ?? 5)) }}</div>
                    <p>{{ $testimonial->content }}</p>
                    <h3>{{ $testimonial->guest_name }}</h3>
                    @if($testimonial->guest_title)<p>{{ $testimonial->guest_title }}</p>@endif
                </article>
            @empty
                <div class="page-card">No testimonials available yet.</div>
            @endforelse
        </div>
        @break

    @case('gallery')
        @if(!empty($pageBody))
            <article class="page-card content-page" style="margin-bottom:1.5rem;">
                <p>{{ $pageBody }}</p>
            </article>
        @endif
        <div class="card-grid gallery-grid">
            @forelse($photos as $photo)
                <article class="gallery-card">
                    @php
                        $galleryImage = \Illuminate\Support\Str::startsWith($photo->image, ['http://', 'https://']) ? $photo->image : asset('storage/' . $photo->image);
                    @endphp
                    <div class="media-block">
                        <button type="button" onclick="openGalleryLightbox('{{ $galleryImage }}', @js($photo->title), @js($photo->description))" style="border:0;background:transparent;padding:0;width:100%;height:100%;cursor:zoom-in;">
                            <img src="{{ $galleryImage }}" alt="{{ $photo->title }}">
                        </button>
                    </div>
                    <h3>{{ $photo->title }}</h3>
                    @if($photo->description)<p>{{ $photo->description }}</p>@endif
                </article>
            @empty
                <div class="page-card">No photos available yet.</div>
            @endforelse
        </div>
        <div class="gallery-lightbox" id="galleryLightbox" onclick="closeGalleryLightbox(event)">
            <button type="button" aria-label="Close" onclick="closeGalleryLightbox(event)">×</button>
            <figure>
                <img id="galleryLightboxImage" src="" alt="">
                <figcaption>
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
                document.getElementById('galleryLightbox').classList.add('active');
            }

            function closeGalleryLightbox(event) {
                if (event.target.id === 'galleryLightbox' || event.target.tagName === 'BUTTON') {
                    document.getElementById('galleryLightbox').classList.remove('active');
                }
            }
        </script>
        @break

    @case('blog')
        @if(!empty($pageBody))
            <article class="page-card content-page" style="margin-bottom:1.5rem;">
                <p>{{ $pageBody }}</p>
            </article>
        @endif
        <div class="card-grid blog-grid">
            @forelse($posts as $post)
                <article class="blog-card">
                    @if($post->featured_image)
                        <div class="media-block"><img src="{{ \Illuminate\Support\Str::startsWith($post->featured_image, ['http://', 'https://']) ? $post->featured_image : asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}"></div>
                    @else
                        <div class="media-block"></div>
                    @endif
                    <p class="content-kicker">{{ optional($post->published_at)->format('M d, Y') }} @if($post->author) by {{ $post->author->name }} @endif</p>
                    <h3>{{ $post->title }}</h3>
                    <p>{{ $post->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($post->content), 120) }}</p>
                    <a class="btn-link" href="{{ route('blog.show', $post->slug) }}">Read More</a>
                </article>
            @empty
                <div class="page-card">No blog posts available yet.</div>
            @endforelse
        </div>
        @break

    @case('blog-show')
        <div class="blog-show-layout">
            <article class="page-card blog-article">
                @if($post->featured_image)
                    <div class="media-block"><img src="{{ \Illuminate\Support\Str::startsWith($post->featured_image, ['http://', 'https://']) ? $post->featured_image : asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}"></div>
                @endif
                <p class="content-kicker">{{ optional($post->published_at)->format('M d, Y') }} @if($post->author) by {{ $post->author->name }} @endif</p>
                <div class="article-content">{!! nl2br(e($post->content)) !!}</div>
                <a class="btn-link" href="{{ route('blog.index') }}">Back to Blog</a>
            </article>
            <aside class="page-card blog-sidebar">
                <h3>Book a Room</h3>
                <p>Turn your reading into a stay. Choose an available room and reserve directly.</p>
                @forelse(($roomTypes ?? collect()) as $roomType)
                    <div class="mini-room">
                        <strong>{{ $roomType->name }}</strong>
                        <p>{{ $roomType->available_count ?? 0 }} available from {{ $currency }}{{ number_format((float) ($roomType->base_price ?? $roomType->price ?? 0), 0) }}</p>
                    </div>
                @empty
                    <p>Rooms are being updated. Contact reservations for availability.</p>
                @endforelse
                <a class="btn-link" href="{{ route('rooms') }}">Check Availability</a>
                @if(!empty($contactPhone))<p style="margin-top:1rem;">Reservations: {{ $contactPhone }}</p>@endif
            </aside>
        </div>
        @if(($relatedPosts ?? collect())->isNotEmpty())
            <section class="related-section">
                <h2>You may also be interested in</h2>
                <div class="card-grid">
                    @foreach($relatedPosts as $related)
                        <article class="blog-card">
                            @if($related->featured_image)
                                <div class="media-block"><img src="{{ \Illuminate\Support\Str::startsWith($related->featured_image, ['http://', 'https://']) ? $related->featured_image : asset('storage/' . $related->featured_image) }}" alt="{{ $related->title }}"></div>
                            @endif
                            <h3>{{ $related->title }}</h3>
                            <p>{{ $related->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($related->content), 120) }}</p>
                            <a class="btn-link" href="{{ route('blog.show', $related->slug) }}">Read More</a>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
        @break

    @case('page')
        <article class="page-card content-page">
            {!! \App\Support\HtmlSanitizer::clean($page->content) !!}
        </article>
        @break

    @case('contact')
        @php
            $mapValue = trim((string) ($mapEmbedUrl ?? $mapAddress ?? ''));
            preg_match('/src=["\']([^"\']+)["\']/', $mapValue, $mapMatch);
            $mapSrc = $mapMatch[1] ?? $mapValue;
            $canRenderMap = \Illuminate\Support\Str::startsWith($mapSrc, ['http://', 'https://']);
        @endphp
        @if(!empty($pageBody))
            <article class="page-card content-page" style="margin-bottom:1.5rem;">
                <p>{{ $pageBody }}</p>
            </article>
        @endif
        <div class="card-grid contact-grid">
            <div class="page-card contact-card"><span class="content-kicker">Call or email</span><h3>Reservations</h3><p>{{ $contactPhone }}</p><p>{{ $contactEmail }}</p></div>
            <div class="page-card contact-card"><span class="content-kicker">Visit</span><h3>Address</h3><p>{{ $physicalAddress ?: 'Address will be available soon.' }}</p></div>
            <div class="page-card contact-card"><span class="content-kicker">Support</span><h3>Guest Support</h3><p>{{ $pageBody ?: 'Our team is available for booking help, special requests, and stay support.' }}</p></div>
        </div>
        @if($canRenderMap)
            <div class="page-card map-card">
                <iframe src="{{ $mapSrc }}" width="100%" height="420" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        @else
            <div class="page-card map-card empty-map">
                <h3>Map</h3>
                <p>Map embed is not configured yet.</p>
            </div>
        @endif
        <div class="page-card visit-card">
            <h3>Visit Us</h3>
            <p>{{ $physicalAddress ?: 'Address will be available soon.' }}</p>
        </div>
        @break

    @case('services')
        @if(!empty($pageBody))
            <article class="page-card content-page" style="margin-bottom:1.5rem;">
                <p>{{ $pageBody }}</p>
            </article>
        @endif
        <div class="card-grid service-grid">
            @foreach(['Concierge Service', 'Fine Dining', 'Spa & Wellness', 'Airport Transfers', 'High-Speed WiFi', 'Event Spaces'] as $service)
                <div class="page-card service-card"><span class="service-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $service }}</h3><p>Designed to make each stay easier, calmer, and more memorable.</p></div>
            @endforeach
        </div>
        @break

    @case('privacy')
    @case('terms')
        <article class="page-card content-page">
            @if(!empty($body))
                {!! nl2br(e($body)) !!}
            @else
                <p>{{ $subtitle ?: 'This page is part of the themed public website experience.' }}</p>
            @endif
        </article>
        @break

    @case('about')
        <article class="page-card content-page">
            <h2>{{ $aboutTitle ?? $title }}</h2>
            @if(!empty($aboutDescription))
                <p>{{ $aboutDescription }}</p>
            @elseif(!empty($subtitle))
                <p>{{ $subtitle }}</p>
            @endif
            @if(!empty($aboutHistoryText))
                <p>{{ $aboutHistoryText }}</p>
            @endif
        </article>
        @break

    @default
        <article class="page-card content-page">
            <p>{{ $subtitle ?: 'This page is part of the themed public website experience.' }}</p>
            @if(!empty($welcomeDescription))
                <p>{{ $welcomeDescription }}</p>
            @endif
        </article>
@endswitch
