@extends('layouts.app')

@section('title', 'Blog Publishing')

@section('content')
<div class="blog-admin animate-fade-in">
    <div class="glass-panel blog-admin-hero">
        <div>
            <p class="blog-kicker"><i class="fa-solid fa-newspaper"></i> SEO Publishing Studio</p>
            <h2>Blog Posts</h2>
            <p>Create discoverable hotel content with clean URLs, structured metadata, search previews, and publication controls.</p>
        </div>
        <button type="button" class="btn btn-primary" onclick="openAddModal()">
            <i class="fa-solid fa-pen-nib"></i> New SEO Post
        </button>
    </div>

    @unless($seoColumnsReady)
        <div class="alert alert-warning">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span>SEO database fields are pending. Run migrations to save meta title, description, keywords, focus keyword, and canonical URL.</span>
        </div>
    @endunless

    <div class="blog-list">
        @forelse($posts as $post)
            @php
                $metaTitle = $post->meta_title ?? $post->title;
                $metaDescription = $post->meta_description ?? $post->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($post->content), 155);
                $wordCount = str_word_count(strip_tags($post->content));
                $readingMinutes = max(1, (int) ceil($wordCount / 220));
            @endphp
            <article class="glass-panel blog-row">
                <div class="blog-row-main">
                    <div class="blog-thumb">
                        @if($post->featured_image)
                            <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}">
                        @else
                            <i class="fa-solid fa-image"></i>
                        @endif
                    </div>
                    <div>
                        <div class="blog-row-meta">
                            <span class="status-pill {{ $post->is_published ? 'published' : 'draft' }}">{{ $post->is_published ? 'Published' : 'Draft' }}</span>
                            <span>{{ $readingMinutes }} min read</span>
                            <span>{{ $wordCount }} words</span>
                            <span>{{ optional($post->published_at)->format('M d, Y') ?: 'Not published' }}</span>
                        </div>
                        <h3>{{ $post->title }}</h3>
                        <p>{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 150) }}</p>
                        <div class="serp-mini">
                            <strong>{{ \Illuminate\Support\Str::limit($metaTitle, 64) }}</strong>
                            <span>{{ url('/blog/' . $post->slug) }}</span>
                            <p>{{ \Illuminate\Support\Str::limit($metaDescription, 160) }}</p>
                        </div>
                    </div>
                </div>
                <div class="blog-actions">
                    @if($post->is_published)
                        <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="btn btn-outline">
                            <i class="fa-solid fa-up-right-from-square"></i> View
                        </a>
                    @endif
                    <button type="button" class="btn btn-outline" onclick='editBlog(@json($post))'>
                        <i class="fa-solid fa-pen-to-square"></i> Edit
                    </button>
                    <form method="POST" action="{{ route('admin.blog.delete', $post) }}" onsubmit="return confirm('Delete this post permanently?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline danger-action">
                            <i class="fa-solid fa-trash-can"></i> Delete
                        </button>
                    </form>
                </div>
            </article>
        @empty
            <div class="glass-panel empty-blog">
                <i class="fa-solid fa-newspaper"></i>
                <h3>No blog posts yet</h3>
                <p>Publish travel guides, hotel announcements, destination articles, and SEO landing content from here.</p>
                <button type="button" class="btn btn-primary" onclick="openAddModal()">Create First Post</button>
            </div>
        @endforelse
    </div>
</div>

<div id="blogModal" class="modal">
    <div class="modal-content blog-modal">
        <div class="blog-modal-head">
            <div>
                <h2 id="blogModalTitle">Create SEO Blog Post</h2>
                <p>Write guest-friendly content with search metadata before publishing.</p>
            </div>
            <button type="button" onclick="closeModal()" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form id="blogForm" method="POST" action="" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="blogMethod" value="POST" disabled>

            <div class="blog-editor-grid">
                <div class="blog-editor-main">
                    <div class="form-group">
                        <label class="form-label">Post Title</label>
                        <input type="text" name="title" id="title" class="form-control" maxlength="255" required oninput="syncSlug(); updateSeoPreview();">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">URL Slug</label>
                            <input type="text" name="slug" id="slug" class="form-control" maxlength="255" required oninput="manualSlug = true; updateSeoPreview();">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Focus Keyword</label>
                            <input type="text" name="focus_keyword" id="focus_keyword" class="form-control" maxlength="120" placeholder="luxury hotel in Lagos" oninput="updateSeoPreview();">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Excerpt / Social Summary</label>
                        <textarea name="excerpt" id="excerpt" rows="3" class="form-control" oninput="updateSeoPreview();" maxlength="500"></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Article Content</label>
                        <textarea name="content" id="content" rows="12" class="form-control content-editor" required oninput="updateSeoPreview();"></textarea>
                    </div>
                </div>

                <aside class="blog-seo-panel">
                    <div class="form-group">
                        <label class="form-label">Meta Title <span id="metaTitleCount">0/60</span></label>
                        <input type="text" name="meta_title" id="meta_title" class="form-control" maxlength="255" oninput="updateSeoPreview();">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Meta Description <span id="metaDescriptionCount">0/160</span></label>
                        <textarea name="meta_description" id="meta_description" rows="4" class="form-control" maxlength="320" oninput="updateSeoPreview();"></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Meta Keywords</label>
                        <input type="text" name="meta_keywords" id="meta_keywords" class="form-control" maxlength="500" placeholder="hotel, suites, travel">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Canonical URL</label>
                        <input type="url" name="canonical_url" id="canonical_url" class="form-control" maxlength="500" placeholder="https://example.com/blog/post">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Featured Image</label>
                        <input type="file" name="featured_image" id="featured_image" class="form-control" accept="image/*">
                    </div>

                    <label class="publish-toggle">
                        <input type="checkbox" name="is_published" id="is_published" value="1">
                        <span>Publish immediately</span>
                    </label>

                    <div class="seo-score-card">
                        <h3>SEO Checklist</h3>
                        <ul id="seoChecklist">
                            <li data-check="title"><i class="fa-solid fa-circle"></i> Meta title is 40-60 characters</li>
                            <li data-check="description"><i class="fa-solid fa-circle"></i> Meta description is 120-160 characters</li>
                            <li data-check="keyword"><i class="fa-solid fa-circle"></i> Focus keyword appears in title/content</li>
                            <li data-check="content"><i class="fa-solid fa-circle"></i> Article is at least 300 words</li>
                            <li data-check="slug"><i class="fa-solid fa-circle"></i> Slug is clean and readable</li>
                        </ul>
                    </div>

                    <div class="serp-preview">
                        <span>Google Preview</span>
                        <strong id="previewTitle">Your meta title appears here</strong>
                        <em id="previewUrl">{{ url('/blog') }}/post-slug</em>
                        <p id="previewDescription">Your meta description appears here.</p>
                    </div>
                </aside>
            </div>

            <div class="blog-modal-actions">
                <button type="button" class="btn btn-outline" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Post</button>
            </div>
        </form>
    </div>
</div>

<style>
    .blog-admin { display: flex; flex-direction: column; gap: 1.5rem; }
    .blog-admin-hero { padding: 2rem; border-left: 5px solid var(--primary); display: flex; justify-content: space-between; align-items: center; gap: 1.5rem; flex-wrap: wrap; }
    .blog-kicker { color: var(--primary); font-weight: 800; text-transform: uppercase; font-size: .8rem; margin: 0 0 .5rem; }
    .blog-admin-hero h2 { margin: 0 0 .5rem; font-size: 1.8rem; }
    .blog-admin-hero p { color: var(--text-secondary); margin: 0; max-width: 720px; line-height: 1.6; }
    .blog-list { display: grid; gap: 1rem; }
    .blog-row { padding: 1.25rem; display: flex; justify-content: space-between; gap: 1.25rem; align-items: stretch; }
    .blog-row-main { display: grid; grid-template-columns: 150px 1fr; gap: 1rem; min-width: 0; }
    .blog-thumb { height: 120px; border-radius: var(--radius-sm); background: var(--primary-glow); display: flex; align-items: center; justify-content: center; overflow: hidden; color: var(--primary); font-size: 1.5rem; }
    .blog-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .blog-row-meta { display: flex; gap: .5rem; flex-wrap: wrap; color: var(--text-secondary); font-size: .78rem; font-weight: 700; margin-bottom: .5rem; }
    .blog-row h3 { margin: 0 0 .4rem; font-size: 1.2rem; }
    .blog-row p { color: var(--text-secondary); line-height: 1.5; margin: 0 0 .7rem; }
    .status-pill { border-radius: var(--radius-full); padding: .2rem .55rem; }
    .status-pill.published { background: var(--success-glow); color: var(--success); }
    .status-pill.draft { background: var(--warning-glow); color: var(--warning); }
    .serp-mini { border-left: 3px solid var(--primary); padding-left: .75rem; display: grid; gap: .15rem; }
    .serp-mini strong { color: #1a0dab; font-size: .92rem; }
    [data-theme="dark"] .serp-mini strong { color: #8ab4f8; }
    .serp-mini span { color: #188038; font-size: .78rem; }
    .serp-mini p { font-size: .82rem; margin: 0; }
    .blog-actions { display: flex; flex-direction: column; justify-content: center; gap: .5rem; min-width: 130px; }
    .blog-actions form { margin: 0; }
    .danger-action { border-color: var(--danger-glow) !important; color: var(--danger) !important; }
    .empty-blog { padding: 4rem 2rem; text-align: center; }
    .empty-blog i { color: var(--text-muted); font-size: 2.5rem; margin-bottom: 1rem; }
    .blog-modal { max-width: 1180px; padding: 0; overflow: hidden; }
    .blog-modal-head { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; gap: 1rem; }
    .blog-modal-head h2 { margin: 0 0 .25rem; }
    .blog-modal-head p { margin: 0; color: var(--text-secondary); }
    .blog-modal-head button { width: 36px; height: 36px; border-radius: 50%; border: 1px solid var(--border-color); background: transparent; color: var(--text-primary); cursor: pointer; }
    .blog-editor-grid { display: grid; grid-template-columns: minmax(0, 1fr) 360px; gap: 1.25rem; padding: 1.5rem; max-height: 74vh; overflow-y: auto; }
    .content-editor { min-height: 300px; line-height: 1.65; }
    .blog-seo-panel { border-left: 1px solid var(--border-color); padding-left: 1.25rem; }
    .form-label span { float: right; color: var(--text-muted); font-size: .75rem; }
    .publish-toggle { display: flex; align-items: center; gap: .55rem; padding: .75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); margin-bottom: 1rem; font-weight: 700; }
    .seo-score-card, .serp-preview { border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1rem; margin-bottom: 1rem; background: rgba(255,255,255,.03); }
    .seo-score-card h3 { margin: 0 0 .75rem; font-size: 1rem; }
    .seo-score-card ul { list-style: none; padding: 0; margin: 0; display: grid; gap: .5rem; color: var(--text-secondary); font-size: .85rem; }
    .seo-score-card li.pass { color: var(--success); }
    .seo-score-card li.pass i { color: var(--success); }
    .serp-preview span { color: var(--text-muted); font-size: .75rem; font-weight: 800; text-transform: uppercase; }
    .serp-preview strong { display: block; color: #1a0dab; font-size: 1rem; margin-top: .35rem; }
    [data-theme="dark"] .serp-preview strong { color: #8ab4f8; }
    .serp-preview em { display: block; color: #188038; font-style: normal; font-size: .82rem; margin: .15rem 0; word-break: break-all; }
    .serp-preview p { color: var(--text-secondary); margin: 0; font-size: .88rem; line-height: 1.45; }
    .blog-modal-actions { display: flex; justify-content: flex-end; gap: .75rem; padding: 1rem 1.5rem; border-top: 1px solid var(--border-color); }
    @media (max-width: 900px) {
        .blog-row, .blog-row-main, .blog-editor-grid { grid-template-columns: 1fr; display: grid; }
        .blog-actions { flex-direction: row; flex-wrap: wrap; }
        .blog-seo-panel { border-left: 0; border-top: 1px solid var(--border-color); padding-left: 0; padding-top: 1rem; }
    }
</style>

<script>
    let manualSlug = false;

    function slugify(value) {
        const stopWords = new Set([
            'a', 'an', 'and', 'are', 'as', 'at', 'be', 'but', 'by', 'for',
            'from', 'has', 'in', 'into', 'is', 'it', 'of', 'on', 'or', 'our',
            'that', 'the', 'their', 'this', 'to', 'with', 'your', 'you'
        ]);

        return value.toString().toLowerCase().trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .split(/\s+/)
            .filter(word => word && !stopWords.has(word))
            .join(' ')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .substring(0, 90);
    }

    function syncSlug() {
        if (!manualSlug) {
            document.getElementById('slug').value = slugify(document.getElementById('title').value);
        }
    }

    function setFormMethod(method) {
        const methodInput = document.getElementById('blogMethod');
        if (method === 'PUT') {
            methodInput.disabled = false;
            methodInput.value = 'PUT';
        } else {
            methodInput.disabled = true;
            methodInput.value = 'POST';
        }
    }

    function openAddModal() {
        manualSlug = false;
        document.getElementById('blogModalTitle').textContent = 'Create SEO Blog Post';
        document.getElementById('blogForm').reset();
        document.getElementById('blogForm').action = "{{ route('admin.blog.store') }}";
        setFormMethod('POST');
        updateSeoPreview();
        document.getElementById('blogModal').classList.add('active');
    }

    function closeModal() {
        document.getElementById('blogModal').classList.remove('active');
    }

    function editBlog(post) {
        manualSlug = true;
        document.getElementById('blogModalTitle').textContent = 'Edit SEO Blog Post';
        document.getElementById('blogForm').action = `/admin/blog/${post.id}/update`;
        setFormMethod('POST');
        document.getElementById('title').value = post.title || '';
        document.getElementById('slug').value = post.slug || '';
        document.getElementById('excerpt').value = post.excerpt || '';
        document.getElementById('content').value = post.content || '';
        document.getElementById('meta_title').value = post.meta_title || '';
        document.getElementById('meta_description').value = post.meta_description || '';
        document.getElementById('meta_keywords').value = post.meta_keywords || '';
        document.getElementById('focus_keyword').value = post.focus_keyword || '';
        document.getElementById('canonical_url').value = post.canonical_url || '';
        document.getElementById('is_published').checked = Boolean(post.is_published);
        updateSeoPreview();
        document.getElementById('blogModal').classList.add('active');
    }

    function updateSeoPreview() {
        const title = document.getElementById('title').value;
        const slug = document.getElementById('slug').value || slugify(title) || 'post-slug';
        const metaTitle = document.getElementById('meta_title').value || title || 'Your meta title appears here';
        const excerpt = document.getElementById('excerpt').value;
        const content = document.getElementById('content').value;
        const metaDescription = document.getElementById('meta_description').value || excerpt || 'Your meta description appears here.';
        const focusKeyword = document.getElementById('focus_keyword').value.toLowerCase();
        const words = content.trim() ? content.trim().split(/\s+/).length : 0;

        document.getElementById('metaTitleCount').textContent = `${metaTitle.length}/60`;
        document.getElementById('metaDescriptionCount').textContent = `${metaDescription.length}/160`;
        document.getElementById('previewTitle').textContent = metaTitle.substring(0, 70);
        document.getElementById('previewUrl').textContent = `{{ url('/blog') }}/${slug}`;
        document.getElementById('previewDescription').textContent = metaDescription.substring(0, 180);

        setCheck('title', metaTitle.length >= 40 && metaTitle.length <= 60);
        setCheck('description', metaDescription.length >= 120 && metaDescription.length <= 160);
        setCheck('keyword', focusKeyword.length > 0 && (metaTitle.toLowerCase().includes(focusKeyword) || content.toLowerCase().includes(focusKeyword)));
        setCheck('content', words >= 300);
        setCheck('slug', /^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(slug));
    }

    function setCheck(name, passed) {
        const item = document.querySelector(`[data-check="${name}"]`);
        item.classList.toggle('pass', passed);
        item.querySelector('i').className = passed ? 'fa-solid fa-circle-check' : 'fa-solid fa-circle';
    }
</script>
@endsection
