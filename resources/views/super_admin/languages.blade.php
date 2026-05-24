@extends('layouts.app')

@section('content')
<div class="admin-page-shell animate-fade-in">
    <div class="glass-panel admin-page-header">
        <div>
            <h1 class="admin-page-title">Localization</h1>
            <p class="admin-page-subtitle">Control available languages and edit translation strings used across the guest website and dashboard.</p>
        </div>
        <div class="admin-card-actions" style="margin-top:0;">
            <form action="{{ route('super_admin.translations.sync') }}" method="POST" style="margin:0;">
                @csrf
                <button type="submit" class="btn btn-outline">
                    <i class="fa-solid fa-rotate"></i> Sync English
                </button>
            </form>
            <button type="button" onclick="openTranslationModal()" class="btn btn-outline">
                <i class="fa-solid fa-pen-to-square"></i> Add Translation
            </button>
            <button type="button" onclick="openLanguageModal()" class="btn btn-primary">
                <i class="fa-solid fa-language"></i> Add Language
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:1rem;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger" style="margin-bottom:1rem;">{{ session('error') }}</div>
    @endif

    <div class="glass-panel">
        @if($languages->isNotEmpty())
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Language</th>
                            <th>Code</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th>Default</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($languages as $language)
                            <tr>
                                <td><strong style="color:var(--text-primary);">{{ $language->flag_emoji }} {{ $language->name }}</strong></td>
                                <td><code>{{ $language->code }}</code></td>
                                <td>{{ $language->order }}</td>
                                <td><span class="status-pill {{ $language->is_active ? 'active' : '' }}">{{ $language->is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td>
                                    @if($language->is_default)
                                        <span class="status-pill featured">Default</span>
                                    @else
                                        <span class="status-pill">Optional</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="admin-card-actions" style="margin-top:0;">
                                        <button type="button" class="btn btn-outline" onclick='editLanguage(@json($language))'>
                                            <i class="fa-solid fa-pen"></i> Edit
                                        </button>
                                        @if(!$language->is_default)
                                            <form method="POST" action="{{ route('super_admin.languages.delete', $language) }}" onsubmit="return confirm('Delete this language?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash"></i> Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">No languages configured yet.</div>
        @endif
    </div>

    <div class="glass-panel" style="margin-top:1.5rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-bottom:1rem;flex-wrap:wrap;">
            <div>
                <h2 style="margin:0;color:var(--text-primary);font-size:1.15rem;">Editable Translations</h2>
                <p class="admin-page-subtitle" style="margin:0.25rem 0 0;">Use stable keys like <code>homepage.hero_title</code> or <code>dashboard.bookings</code>.</p>
            </div>
            @if(Route::has('locale.set') && $languages->isNotEmpty())
                <form action="{{ route('locale.set') }}" method="POST" style="display:flex;align-items:center;gap:0.5rem;">
                    @csrf
                    <select name="locale" class="form-control" onchange="this.form.submit()" style="min-width:140px;">
                        @foreach($languages->where('is_active', true) as $language)
                            <option value="{{ $language->code }}" {{ app()->getLocale() === $language->code ? 'selected' : '' }}>{{ $language->flag_emoji }} {{ $language->name }}</option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>

        @if($translations instanceof \Illuminate\Contracts\Pagination\Paginator && $translations->count())
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Locale</th>
                            <th>Group</th>
                            <th>Key</th>
                            <th>English Source</th>
                            <th>Value</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($translations as $translation)
                            <tr>
                                <td><code>{{ $translation->locale }}</code></td>
                                <td>{{ $translation->group }}</td>
                                <td><code>{{ $translation->key }}</code></td>
                                <td style="max-width:300px;">{{ \Illuminate\Support\Str::limit($translation->source_text, 80) }}</td>
                                <td style="max-width:420px;">{{ \Illuminate\Support\Str::limit($translation->value, 120) }}</td>
                                <td>
                                    <div class="admin-card-actions" style="margin-top:0;">
                                        <button type="button" class="btn btn-outline" onclick='editTranslation(@json($translation))'><i class="fa-solid fa-pen"></i> Edit</button>
                                        <form method="POST" action="{{ route('super_admin.translations.delete', $translation) }}" onsubmit="return confirm('Delete this translation?')">
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
            <div style="margin-top:1rem;">{{ $translations->links() }}</div>
        @else
            <div class="empty-state">No editable translations configured yet.</div>
        @endif
    </div>
</div>

<div id="languageModal" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h2 id="languageModalTitle" style="margin:0;color:var(--text-primary);">Add Language</h2>
            <button type="button" class="btn btn-outline" onclick="closeLanguageModal()" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="languageForm" method="POST" action="{{ route('super_admin.languages.store') }}">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="name">Language Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="code">Code</label>
                    <input type="text" name="code" id="code" class="form-control" maxlength="5" placeholder="en" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="flag_emoji">Flag Emoji</label>
                    <input type="text" name="flag_emoji" id="flag_emoji" class="form-control" maxlength="2" placeholder="US">
                </div>
                <div class="form-group">
                    <label class="form-label" for="order">Display Order</label>
                    <input type="number" name="order" id="order" class="form-control" min="0" value="0">
                </div>
            </div>
            <div class="form-row">
                <label class="form-checkbox"><input type="checkbox" name="is_active" id="is_active" value="1" checked> <span>Active</span></label>
                <label class="form-checkbox"><input type="checkbox" name="is_default" id="is_default" value="1"> <span>Default language</span></label>
            </div>
            <div class="admin-card-actions" style="justify-content:flex-end;">
                <button type="button" class="btn btn-outline" onclick="closeLanguageModal()">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Language</button>
            </div>
        </form>
    </div>
</div>

<div id="translationModal" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h2 id="translationModalTitle" style="margin:0;color:var(--text-primary);">Add Translation</h2>
            <button type="button" class="btn btn-outline" onclick="closeTranslationModal()" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="translationForm" method="POST" action="{{ route('super_admin.translations.store') }}">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="translation_locale">Locale</label>
                    <select name="locale" id="translation_locale" class="form-control" required>
                        @foreach($languages as $language)
                            <option value="{{ $language->code }}">{{ $language->flag_emoji }} {{ $language->name }} ({{ $language->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="translation_group">Group</label>
                    <input type="text" name="group" id="translation_group" class="form-control" value="general" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="translation_key">Translation Key</label>
                <input type="text" name="key" id="translation_key" class="form-control" placeholder="homepage.hero_title" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="translation_source_text">English Source Text</label>
                <textarea name="source_text" id="translation_source_text" class="form-control" rows="3" placeholder="Original English phrase used in the project"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label" for="translation_value">Translated Text</label>
                <textarea name="value" id="translation_value" class="form-control" rows="5"></textarea>
            </div>
            <div class="admin-card-actions" style="justify-content:flex-end;">
                <button type="button" class="btn btn-outline" onclick="closeTranslationModal()">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Translation</button>
            </div>
        </form>
    </div>
</div>

<script>
function openLanguageModal() {
    const form = document.getElementById('languageForm');
    form.reset();
    form.action = "{{ route('super_admin.languages.store') }}";
    document.getElementById('languageModalTitle').textContent = 'Add Language';
    document.getElementById('is_active').checked = true;
    document.getElementById('languageModal').classList.add('active');
}

function editLanguage(language) {
    const form = document.getElementById('languageForm');
    form.action = `/super-admin/languages/${language.id}/update`;
    document.getElementById('languageModalTitle').textContent = 'Edit Language';
    document.getElementById('name').value = language.name || '';
    document.getElementById('code').value = language.code || '';
    document.getElementById('flag_emoji').value = language.flag_emoji || '';
    document.getElementById('order').value = language.order || 0;
    document.getElementById('is_active').checked = Boolean(language.is_active);
    document.getElementById('is_default').checked = Boolean(language.is_default);
    document.getElementById('languageModal').classList.add('active');
}

function closeLanguageModal() {
    document.getElementById('languageModal').classList.remove('active');
}

function openTranslationModal() {
    const form = document.getElementById('translationForm');
    form.reset();
    form.action = "{{ route('super_admin.translations.store') }}";
    document.getElementById('translationModalTitle').textContent = 'Add Translation';
    document.getElementById('translation_group').value = 'general';
    document.getElementById('translation_source_text').value = '';
    document.getElementById('translationModal').classList.add('active');
}

function editTranslation(translation) {
    const form = document.getElementById('translationForm');
    form.action = `/super-admin/translations/${translation.id}/update`;
    document.getElementById('translationModalTitle').textContent = 'Edit Translation';
    document.getElementById('translation_locale').value = translation.locale || '';
    document.getElementById('translation_group').value = translation.group || 'general';
    document.getElementById('translation_key').value = translation.key || '';
    document.getElementById('translation_source_text').value = translation.source_text || '';
    document.getElementById('translation_value').value = translation.value || '';
    document.getElementById('translationModal').classList.add('active');
}

function closeTranslationModal() {
    document.getElementById('translationModal').classList.remove('active');
}
</script>
@endsection
