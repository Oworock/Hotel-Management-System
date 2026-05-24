@extends('layouts.app')

@section('content')
<div class="admin-page-shell animate-fade-in">
    <div class="glass-panel admin-page-header">
        <div>
            <h1 class="admin-page-title">Testimonial Management</h1>
            <p class="admin-page-subtitle">Manage guest stories, ratings, and featured reviews for the public website.</p>
        </div>
        <button type="button" onclick="openTestimonialModal()" class="btn btn-primary">
            <i class="fa-solid fa-comment-medical"></i> Add Testimonial
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:1rem;">{{ session('success') }}</div>
    @endif

    <div class="admin-grid">
        @forelse($testimonials as $testimonial)
            <article class="admin-card">
                <div style="display:flex;justify-content:space-between;gap:1rem;align-items:flex-start;">
                    <div>
                        <h3 class="admin-card-title">{{ $testimonial->guest_name }}</h3>
                        <p class="admin-card-text">{{ $testimonial->guest_title ?: 'Guest' }}</p>
                    </div>
                    <span style="color:#f59e0b;white-space:nowrap;">{{ str_repeat('★', (int) $testimonial->rating) }}</span>
                </div>
                <p class="admin-card-text" style="margin-top:1rem;">{{ \Illuminate\Support\Str::limit($testimonial->content, 150) }}</p>
                <div class="admin-card-actions">
                    <span class="status-pill {{ $testimonial->is_active ? 'active' : '' }}">{{ $testimonial->is_active ? 'Active' : 'Inactive' }}</span>
                    @if($testimonial->is_featured)
                        <span class="status-pill featured">Featured</span>
                    @endif
                </div>
                <div class="admin-card-actions">
                    <button type="button" class="btn btn-outline" onclick='editTestimonial(@json($testimonial))'><i class="fa-solid fa-pen"></i> Edit</button>
                    <form method="POST" action="{{ route('admin.testimonials.delete', $testimonial) }}" onsubmit="return confirm('Delete this testimonial?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash"></i> Delete</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="glass-panel empty-state" style="grid-column:1 / -1;">No testimonials yet. Add real guest feedback to build trust.</div>
        @endforelse
    </div>
</div>

<div id="testimonialModal" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h2 id="testimonialModalTitle" style="margin:0;color:var(--text-primary);">Add Testimonial</h2>
            <button type="button" class="btn btn-outline" onclick="closeTestimonialModal()" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="testimonialForm" method="POST" action="{{ route('admin.testimonials.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="guest_name">Guest Name</label>
                    <input type="text" name="guest_name" id="guest_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="guest_title">Guest Type</label>
                    <input type="text" name="guest_title" id="guest_title" class="form-control" placeholder="Business traveler">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="content">Testimonial</label>
                <textarea name="content" id="content" rows="5" class="form-control" required></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="rating">Rating</label>
                    <select name="rating" id="rating" class="form-control form-select" required>
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}">{{ $i }} Stars</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="image">Guest Photo</label>
                    <input type="file" name="image" id="image" accept="image/*" class="form-control">
                </div>
            </div>
            <div class="form-row">
                <label class="form-checkbox"><input type="checkbox" name="is_featured" id="is_featured" value="1"> <span>Featured</span></label>
                <label class="form-checkbox"><input type="checkbox" name="is_active" id="is_active" value="1" checked> <span>Active on website</span></label>
            </div>
            <div class="admin-card-actions" style="justify-content:flex-end;">
                <button type="button" class="btn btn-outline" onclick="closeTestimonialModal()">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Testimonial</button>
            </div>
        </form>
    </div>
</div>

<script>
function openTestimonialModal() {
    const form = document.getElementById('testimonialForm');
    form.reset();
    form.action = "{{ route('admin.testimonials.store') }}";
    document.getElementById('testimonialModalTitle').textContent = 'Add Testimonial';
    document.getElementById('rating').value = '5';
    document.getElementById('is_active').checked = true;
    document.getElementById('testimonialModal').classList.add('active');
}

function editTestimonial(testimonial) {
    const form = document.getElementById('testimonialForm');
    form.action = `/admin/testimonials/${testimonial.id}/update`;
    document.getElementById('testimonialModalTitle').textContent = 'Edit Testimonial';
    document.getElementById('guest_name').value = testimonial.guest_name || '';
    document.getElementById('guest_title').value = testimonial.guest_title || '';
    document.getElementById('content').value = testimonial.content || '';
    document.getElementById('rating').value = testimonial.rating || '5';
    document.getElementById('is_featured').checked = Boolean(testimonial.is_featured);
    document.getElementById('is_active').checked = Boolean(testimonial.is_active);
    document.getElementById('testimonialModal').classList.add('active');
}

function closeTestimonialModal() {
    document.getElementById('testimonialModal').classList.remove('active');
}
</script>
@endsection
