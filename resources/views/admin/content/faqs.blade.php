@extends('layouts.app')

@section('content')
<div class="admin-page-shell animate-fade-in">
    <div class="glass-panel admin-page-header">
        <div>
            <h1 class="admin-page-title">FAQ Management</h1>
            <p class="admin-page-subtitle">Publish helpful answers guests can scan before booking or arrival.</p>
        </div>
        <button type="button" onclick="openFaqModal()" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Add FAQ
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:1rem;">{{ session('success') }}</div>
    @endif

    <div class="glass-panel">
        @if($faqs->isNotEmpty())
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Question</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($faqs as $faq)
                            <tr>
                                <td>
                                    <strong style="color:var(--text-primary);">{{ $faq->question }}</strong>
                                    <p class="admin-card-text" style="margin-top:0.35rem;">{{ \Illuminate\Support\Str::limit($faq->answer, 130) }}</p>
                                </td>
                                <td>{{ $faq->order }}</td>
                                <td><span class="status-pill {{ $faq->is_active ? 'active' : '' }}">{{ $faq->is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td>
                                    <div class="admin-card-actions" style="margin-top:0;">
                                        <button type="button" class="btn btn-outline" onclick='editFaq(@json($faq))'>
                                            <i class="fa-solid fa-pen"></i> Edit
                                        </button>
                                        <form method="POST" action="{{ route('admin.faqs.delete', $faq) }}" onsubmit="return confirm('Delete this FAQ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash"></i> Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">No FAQs yet. Add the first question guests usually ask.</div>
        @endif
    </div>
</div>

<div id="faqModal" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h2 id="faqModalTitle" style="margin:0;color:var(--text-primary);">Add FAQ</h2>
            <button type="button" class="btn btn-outline" onclick="closeFaqModal()" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="faqForm" method="POST" action="{{ route('admin.faqs.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label" for="question">Question</label>
                <input type="text" name="question" id="question" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="answer">Answer</label>
                <textarea name="answer" id="answer" rows="5" class="form-control" required></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="order">Display Order</label>
                    <input type="number" name="order" id="order" class="form-control" min="0" value="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Visibility</label>
                    <label class="form-checkbox" style="padding-top:0.85rem;">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked>
                        <span>Active on website</span>
                    </label>
                </div>
            </div>
            <div class="admin-card-actions" style="justify-content:flex-end;">
                <button type="button" class="btn btn-outline" onclick="closeFaqModal()">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save FAQ</button>
            </div>
        </form>
    </div>
</div>

<script>
function openFaqModal() {
    const form = document.getElementById('faqForm');
    form.reset();
    form.action = "{{ route('admin.faqs.store') }}";
    document.getElementById('faqModalTitle').textContent = 'Add FAQ';
    document.getElementById('is_active').checked = true;
    document.getElementById('faqModal').classList.add('active');
}

function editFaq(faq) {
    const form = document.getElementById('faqForm');
    form.action = `/admin/faqs/${faq.id}/update`;
    document.getElementById('faqModalTitle').textContent = 'Edit FAQ';
    document.getElementById('question').value = faq.question || '';
    document.getElementById('answer').value = faq.answer || '';
    document.getElementById('order').value = faq.order || 0;
    document.getElementById('is_active').checked = Boolean(faq.is_active);
    document.getElementById('faqModal').classList.add('active');
}

function closeFaqModal() {
    document.getElementById('faqModal').classList.remove('active');
}
</script>
@endsection
