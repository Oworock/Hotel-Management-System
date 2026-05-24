@extends('layouts.app')

@section('title', 'Theme Management')

@section('content')
@php
    $layoutNotes = [
        'modern-minimal' => ['Split hero', 'No slider', 'Horizontal room list', 'Clean business styling'],
        'luxury-premium' => ['Cinematic hero slider', 'Private reservation panel', 'Staggered suite cards', 'Editorial luxury layout'],
        'boutique-charm' => ['Warm image hero', 'Search in hero card', 'Masonry story blocks', 'Staggered room cards'],
        'resort-vibrant' => ['Full-screen slider', 'Floating search bar', 'Rooms first', 'Bright resort sections'],
    ];
@endphp

<div class="theme-admin-page animate-fade-in">
    <div class="glass-panel theme-admin-hero">
        <div>
            <p class="theme-admin-kicker"><i class="fa-solid fa-palette"></i> Public Website Themes</p>
            <h2>Control the full homepage experience</h2>
            <p>
                Selecting a theme now changes the homepage structure, hero behavior, room placement, section rhythm, and visual treatment, not only the colors.
            </p>
        </div>
        <div class="theme-active-summary">
            <span>Active Theme</span>
            <strong>{{ $activeTheme?->name ?? 'None Selected' }}</strong>
            @if($activeTheme)
                <a href="{{ route('home') }}" target="_blank" class="btn btn-outline">
                    <i class="fa-solid fa-up-right-from-square"></i> Preview Homepage
                </a>
            @endif
        </div>
    </div>

    <div class="theme-grid">
        @forelse($themes as $theme)
            @php
                $colors = $theme->colors ?? [];
                $notes = $layoutNotes[$theme->slug] ?? ['Custom homepage layout', 'Theme color controls', 'Responsive structure'];
            @endphp
            <article class="glass-panel theme-card {{ $theme->is_active ? 'active' : '' }}">
                <div class="theme-preview" style="--preview-primary: {{ $colors['primary'] ?? '#6e44ff' }}; --preview-secondary: {{ $colors['secondary'] ?? '#f44496' }}; --preview-bg: {{ $colors['background'] ?? '#f8fafc' }};">
                    <div class="preview-nav"></div>
                    <div class="preview-stage">
                        <div class="preview-copy">
                            <span></span>
                            <strong></strong>
                            <em></em>
                        </div>
                        <div class="preview-media"></div>
                    </div>
                    <div class="preview-rooms">
                        <span></span><span></span><span></span>
                    </div>
                </div>

                <div class="theme-card-body">
                    <div class="theme-title-row">
                        <div>
                            <h3>{{ $theme->name }}</h3>
                            <p>{{ $theme->description }}</p>
                        </div>
                        @if($theme->is_active)
                            <span class="theme-status active"><i class="fa-solid fa-circle-check"></i> Active</span>
                        @else
                            <span class="theme-status">Ready</span>
                        @endif
                    </div>

                    <div class="theme-layout-list">
                        @foreach($notes as $note)
                            <span><i class="fa-solid fa-check"></i> {{ $note }}</span>
                        @endforeach
                    </div>

                    <div class="theme-swatches">
                        @foreach(['primary', 'secondary', 'accent', 'background', 'text'] as $colorKey)
                            <button
                                type="button"
                                title="{{ ucfirst($colorKey) }}: {{ $colors[$colorKey] ?? 'Not set' }}"
                                style="background: {{ $colors[$colorKey] ?? '#d0d5dd' }};"
                            ></button>
                        @endforeach
                    </div>

                    <div class="theme-actions">
                        @if(!$theme->is_active)
                            <form method="POST" action="{{ route('super_admin.themes.activate', $theme) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i> Activate Layout
                                </button>
                            </form>
                        @else
                            <a href="{{ route('home') }}" target="_blank" class="btn btn-primary">
                                <i class="fa-solid fa-eye"></i> View Live
                            </a>
                        @endif

                        <button type="button" class="btn btn-outline" onclick='openColorModal(@json($theme))'>
                            <i class="fa-solid fa-droplet"></i> Colors
                        </button>
                        <button type="button" class="btn btn-outline" onclick='openContentModal(@json($theme))'>
                            <i class="fa-solid fa-pen-to-square"></i> Content
                        </button>
                    </div>

                    <div class="theme-meta">
                        <span>By {{ $theme->author ?: 'StayFlow' }}</span>
                        <span>v{{ $theme->version }}</span>
                    </div>
                </div>
            </article>
        @empty
            <div class="glass-panel" style="padding: 3rem; text-align: center; grid-column: 1 / -1;">
                <i class="fa-solid fa-palette" style="font-size: 2.5rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
                <h3>No themes found</h3>
                <p style="color: var(--text-secondary);">Run the theme seeder to install default homepage themes.</p>
            </div>
        @endforelse
    </div>
</div>

<div id="colorModal" class="modal">
    <div class="modal-content theme-color-modal">
        <div class="modal-header">
            <div>
                <h2>Customize Theme Colors</h2>
                <p id="modalThemeName">Theme palette</p>
            </div>
            <button type="button" onclick="closeColorModal()" aria-label="Close">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="colorForm" method="POST" action="">
            @csrf
            <div class="color-grid">
                @foreach(['primary', 'secondary', 'accent', 'background', 'text'] as $colorKey)
                    <label class="color-field">
                        <span>{{ ucfirst($colorKey) }}</span>
                        <input type="color" name="{{ $colorKey }}" id="{{ $colorKey }}" value="#000000">
                    </label>
                @endforeach
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-outline" onclick="closeColorModal()">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Colors</button>
            </div>
        </form>
    </div>
</div>

<div id="contentModal" class="modal">
    <div class="modal-content theme-content-modal">
        <div class="modal-header">
            <div>
                <h2>Customize Theme Content</h2>
                <p id="contentModalThemeName">Theme copy and menu options</p>
            </div>
            <button type="button" onclick="closeContentModal()" aria-label="Close">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="contentForm" method="POST" action="">
            @csrf
            <div class="content-grid">
                <label class="content-field">
                    <span>Hero Label</span>
                    <input type="text" name="hero_label" id="content_hero_label" maxlength="120" placeholder="Hotel name or page label">
                </label>
                <label class="content-field">
                    <span>Booking Panel Title</span>
                    <input type="text" name="booking_title" id="content_booking_title" maxlength="120" placeholder="Plan your stay">
                </label>
                <label class="content-field">
                    <span>Side Panel Title</span>
                    <input type="text" name="side_title" id="content_side_title" maxlength="120" placeholder="Guest note">
                </label>
                <label class="content-field">
                    <span>Header CTA Label</span>
                    <input type="text" name="nav_cta_label" id="content_nav_cta_label" maxlength="40" placeholder="Book">
                </label>
                <label class="content-field wide">
                    <span>Side Panel Text</span>
                    <textarea name="side_text" id="content_side_text" rows="4" maxlength="500" placeholder="Short support copy used in the theme side panel."></textarea>
                </label>
                <label class="content-field wide">
                    <span>Footer Note</span>
                    <textarea name="footer_note" id="content_footer_note" rows="3" maxlength="500" placeholder="Custom footer support text for this theme."></textarea>
                </label>
                <label class="content-toggle wide">
                    <input type="hidden" name="show_plugin_links" value="0">
                    <input type="checkbox" name="show_plugin_links" id="content_show_plugin_links" value="1">
                    <span>Show public plugin links in this theme header when plugin routes are available</span>
                </label>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-outline" onclick="closeContentModal()">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Content</button>
            </div>
        </form>
    </div>
</div>

<style>
    .theme-admin-page {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .theme-admin-hero {
        padding: 2rem;
        border-left: 5px solid var(--primary);
        display: flex;
        justify-content: space-between;
        gap: 1.5rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .theme-admin-kicker {
        color: var(--primary);
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.8rem;
        margin: 0 0 0.5rem;
    }

    .theme-admin-hero h2 {
        font-size: 1.8rem;
        margin: 0 0 0.5rem;
        color: var(--text-primary);
    }

    .theme-admin-hero p {
        color: var(--text-secondary);
        margin: 0;
        max-width: 720px;
        line-height: 1.6;
    }

    .theme-active-summary {
        min-width: 250px;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        padding: 1rem;
        background: rgba(255, 255, 255, 0.04);
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .theme-active-summary span,
    .theme-meta {
        color: var(--text-secondary);
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .theme-active-summary strong {
        color: var(--text-primary);
        font-size: 1.15rem;
    }

    .theme-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(330px, 1fr));
        gap: 1.5rem;
    }

    .theme-card {
        padding: 0;
        overflow: hidden;
        border: 1px solid var(--border-color);
    }

    .theme-card.active {
        border-color: color-mix(in srgb, var(--primary) 45%, var(--border-color));
        box-shadow: 0 0 0 1px var(--primary-glow), var(--shadow-lg);
    }

    .theme-preview {
        height: 220px;
        padding: 1rem;
        background: var(--preview-bg);
        border-bottom: 1px solid var(--border-color);
    }

    .preview-nav {
        height: 18px;
        width: 48%;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.12);
        margin-bottom: 1rem;
    }

    .preview-stage {
        display: grid;
        grid-template-columns: 1fr 0.9fr;
        gap: 0.75rem;
        height: 112px;
    }

    .preview-copy,
    .preview-media,
    .preview-rooms span {
        border-radius: 8px;
    }

    .preview-copy {
        display: flex;
        flex-direction: column;
        gap: 0.55rem;
        justify-content: center;
    }

    .preview-copy span {
        width: 38%;
        height: 10px;
        border-radius: 999px;
        background: var(--preview-primary);
    }

    .preview-copy strong {
        width: 90%;
        height: 24px;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.2);
    }

    .preview-copy em {
        width: 70%;
        height: 14px;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.12);
    }

    .preview-media {
        background: linear-gradient(135deg, var(--preview-primary), var(--preview-secondary));
    }

    .preview-rooms {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.5rem;
        margin-top: 0.85rem;
    }

    .preview-rooms span {
        height: 36px;
        background: rgba(255, 255, 255, 0.62);
        border: 1px solid rgba(15, 23, 42, 0.08);
    }

    .theme-card-body {
        padding: 1.35rem;
    }

    .theme-title-row {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: start;
    }

    .theme-title-row h3 {
        margin: 0 0 0.4rem;
        color: var(--text-primary);
        font-size: 1.25rem;
    }

    .theme-title-row p {
        margin: 0;
        color: var(--text-secondary);
        line-height: 1.5;
        font-size: 0.92rem;
    }

    .theme-status {
        white-space: nowrap;
        border: 1px solid var(--border-color);
        color: var(--text-secondary);
        border-radius: var(--radius-sm);
        padding: 0.35rem 0.55rem;
        font-size: 0.75rem;
        font-weight: 800;
    }

    .theme-status.active {
        background: var(--success-glow);
        color: var(--success);
        border-color: transparent;
    }

    .theme-layout-list {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin: 1rem 0;
    }

    .theme-layout-list span {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        padding: 0.4rem 0.55rem;
        color: var(--text-secondary);
        font-size: 0.78rem;
        font-weight: 700;
        background: rgba(255, 255, 255, 0.03);
    }

    .theme-layout-list i {
        color: var(--primary);
    }

    .theme-swatches {
        display: flex;
        gap: 0.45rem;
        margin-bottom: 1.15rem;
    }

    .theme-swatches button {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.55);
        box-shadow: 0 0 0 1px var(--border-color);
        cursor: help;
    }

    .theme-actions {
        display: flex;
        gap: 0.6rem;
        flex-wrap: wrap;
        border-top: 1px solid var(--border-color);
        padding-top: 1rem;
    }

    .theme-actions form {
        margin: 0;
    }

    .theme-meta {
        display: flex;
        justify-content: space-between;
        margin-top: 1rem;
    }

    .theme-color-modal {
        max-width: 560px;
        padding: 0;
        overflow: hidden;
    }

    .theme-content-modal {
        max-width: 760px;
        padding: 0;
        overflow: hidden;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        gap: 1rem;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
    }

    .modal-header h2 {
        margin: 0 0 0.3rem;
        font-size: 1.35rem;
    }

    .modal-header p {
        margin: 0;
        color: var(--text-secondary);
    }

    .modal-header button {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 1px solid var(--border-color);
        background: transparent;
        color: var(--text-primary);
        cursor: pointer;
    }

    .color-grid {
        padding: 1.5rem;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .content-grid {
        padding: 1.5rem;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .color-field {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        padding: 0.75rem;
        color: var(--text-primary);
        font-weight: 700;
    }

    .content-field,
    .content-toggle {
        display: grid;
        gap: 0.45rem;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        padding: 0.75rem;
        color: var(--text-primary);
        font-weight: 700;
    }

    .content-field.wide,
    .content-toggle.wide {
        grid-column: 1 / -1;
    }

    .content-field span {
        color: var(--text-secondary);
        font-size: 0.82rem;
        text-transform: uppercase;
    }

    .content-field input,
    .content-field textarea {
        width: 100%;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        background: var(--surface);
        color: var(--text-primary);
        padding: 0.75rem;
    }

    .content-toggle {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: var(--text-secondary);
        line-height: 1.5;
    }

    .color-field input {
        width: 54px;
        height: 38px;
        border: 0;
        background: transparent;
        cursor: pointer;
    }

    .modal-actions {
        border-top: 1px solid var(--border-color);
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }

    @media (max-width: 700px) {
        .theme-grid,
        .color-grid,
        .content-grid {
            grid-template-columns: 1fr;
        }

        .preview-stage {
            grid-template-columns: 1fr;
        }

        .preview-media {
            display: none;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    function openColorModal(theme) {
        const colors = theme.colors || {};
        document.getElementById('colorForm').action = `/super-admin/themes/${theme.id}/colors`;
        document.getElementById('modalThemeName').textContent = theme.name;
        document.getElementById('primary').value = colors.primary || '#3B82F6';
        document.getElementById('secondary').value = colors.secondary || '#1F2937';
        document.getElementById('accent').value = colors.accent || '#10B981';
        document.getElementById('background').value = colors.background || '#F9FAFB';
        document.getElementById('text').value = colors.text || '#111827';
        document.getElementById('colorModal').classList.add('active');
    }

    function closeColorModal() {
        document.getElementById('colorModal').classList.remove('active');
    }

    function openContentModal(theme) {
        const content = (theme.settings && theme.settings.content) || {};
        document.getElementById('contentForm').action = `/super-admin/themes/${theme.id}/content`;
        document.getElementById('contentModalThemeName').textContent = theme.name;
        document.getElementById('content_hero_label').value = content.hero_label || '';
        document.getElementById('content_booking_title').value = content.booking_title || '';
        document.getElementById('content_side_title').value = content.side_title || '';
        document.getElementById('content_side_text').value = content.side_text || '';
        document.getElementById('content_footer_note').value = content.footer_note || '';
        document.getElementById('content_nav_cta_label').value = content.nav_cta_label || '';
        document.getElementById('content_show_plugin_links').checked = content.show_plugin_links !== false && content.show_plugin_links !== '0';
        document.getElementById('contentModal').classList.add('active');
    }

    function closeContentModal() {
        document.getElementById('contentModal').classList.remove('active');
    }
</script>
@endsection
