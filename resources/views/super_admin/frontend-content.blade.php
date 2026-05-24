@extends('layouts.app')

@section('title', 'Frontend Editor')

@section('content')
<div class="admin-page-shell animate-fade-in">
    <div class="glass-panel admin-page-header">
        <div>
            <h1 class="admin-page-title">Frontend Editor</h1>
            <p class="admin-page-subtitle">Edit homepage text, public page headings, footer notes, and the website menu from one place.</p>
        </div>
        <div class="admin-card-actions" style="margin-top:0;">
            <form method="POST" action="{{ route('super_admin.frontend_content.prefill') }}" style="margin:0;">
                @csrf
                <button type="submit" class="btn btn-outline">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Load Demo Text
                </button>
            </form>
            <button type="button" onclick="openMenuModal()" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Add Menu Item
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:1rem;">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger" style="margin-bottom:1rem;">{{ $errors->first() }}</div>
    @endif

    <div class="glass-panel" style="margin-bottom:1.5rem;">
        <div style="display:flex;justify-content:space-between;gap:1rem;align-items:flex-start;flex-wrap:wrap;margin-bottom:1.25rem;">
            <div>
                <h2 style="margin:0;color:var(--text-primary);font-size:1.2rem;">Where To Edit Text</h2>
                <p class="admin-page-subtitle" style="margin:0.25rem 0 0;">Homepage text, public page introductions, legal page copy, and global footer text are below. Detailed records like FAQs, Gallery, Blog, Testimonials, sliders, coupons, and custom pages still use their dedicated dashboard sections.</p>
            </div>
        </div>

        <form action="{{ route('super_admin.frontend_content.update') }}" method="POST">
            @csrf
            @foreach(collect($contentKeys)->groupBy('group', preserveKeys: true) as $group => $fields)
                <div style="border-top:1px solid var(--border-color);padding-top:1.25rem;margin-top:1.25rem;">
                    <h3 style="font-size:1rem;margin:0 0 1rem;color:var(--primary);">{{ $group }}</h3>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1rem;">
                        @foreach($fields as $key => $meta)
                            <div class="form-group">
                                <label class="form-label" for="{{ $key }}">{{ $meta['label'] }}</label>
                                @if($meta['type'] === 'textarea')
                                    <textarea name="{{ $key }}" id="{{ $key }}" class="form-control" rows="4">{{ $settings[$key] ?? '' }}</textarea>
                                @else
                                    <input type="text" name="{{ $key }}" id="{{ $key }}" class="form-control" value="{{ $settings[$key] ?? '' }}">
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
            <div class="admin-card-actions" style="justify-content:flex-end;margin-top:1.5rem;">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Frontend Text</button>
            </div>
        </form>
    </div>

    <div class="glass-panel">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;margin-bottom:1rem;">
            <div>
                <h2 style="margin:0;color:var(--text-primary);font-size:1.2rem;">Website Menu</h2>
                <p class="admin-page-subtitle" style="margin:0.25rem 0 0;">If no menu items are created, the system uses the default Home, Rooms, Services, Gallery, FAQs, Blog, About, Contact menu.</p>
            </div>
            <button type="button" onclick="openMenuModal()" class="btn btn-outline"><i class="fa-solid fa-bars"></i> Add Menu Item</button>
        </div>

        @if($menuItems->isNotEmpty())
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Label</th>
                            <th>Type</th>
                            <th>Destination</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($menuItems as $item)
                            <tr>
                                <td>{{ $item->order }}</td>
                                <td><strong style="color:var(--text-primary);">{{ $item->label }}</strong></td>
                                <td>{{ ucfirst($item->type) }}</td>
                                <td>
                                    @if($item->type === 'page')
                                        {{ $item->page?->title ?? 'Missing page' }}
                                    @elseif($item->type === 'route')
                                        <code>{{ $item->route_name }}</code>
                                    @else
                                        <code>{{ $item->url }}</code>
                                    @endif
                                </td>
                                <td><span class="status-pill {{ $item->is_active ? 'active' : '' }}">{{ $item->is_active ? 'Active' : 'Hidden' }}</span></td>
                                <td>
                                    <div class="admin-card-actions" style="margin-top:0;">
                                        <button type="button" class="btn btn-outline" onclick='editMenuItem(@json($item))'><i class="fa-solid fa-pen"></i> Edit</button>
                                        <form method="POST" action="{{ route('super_admin.navigation.delete', $item) }}" onsubmit="return confirm('Delete this menu item?')" style="margin:0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">No custom menu items yet. The default public menu is active.</div>
        @endif
    </div>
</div>

<div id="menuModal" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h2 id="menuModalTitle" style="margin:0;color:var(--text-primary);">Add Menu Item</h2>
            <button type="button" class="btn btn-outline" onclick="closeMenuModal()" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="menuForm" method="POST" action="{{ route('super_admin.navigation.store') }}">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="label">Menu Label</label>
                    <input type="text" name="label" id="label" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="type">Destination Type</label>
                    <select name="type" id="type" class="form-control" onchange="toggleMenuDestination()" required>
                        <option value="route">System Page</option>
                        <option value="page">Custom Page</option>
                        <option value="url">Custom URL</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group menu-destination menu-route">
                    <label class="form-label" for="route_name">System Page</label>
                    <select name="route_name" id="route_name" class="form-control">
                        @foreach($routeOptions as $routeName => $label)
                            @if(Route::has($routeName))
                                <option value="{{ $routeName }}">{{ $label }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="form-group menu-destination menu-page" style="display:none;">
                    <label class="form-label" for="page_id">Custom Page</label>
                    <select name="page_id" id="page_id" class="form-control">
                        <option value="">Select page</option>
                        @foreach($pages as $page)
                            <option value="{{ $page->id }}">{{ $page->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group menu-destination menu-url" style="display:none;">
                    <label class="form-label" for="url">URL</label>
                    <input type="text" name="url" id="url" class="form-control" placeholder="https://example.com or /path">
                </div>
                <div class="form-group">
                    <label class="form-label" for="order">Order</label>
                    <input type="number" name="order" id="order" class="form-control" min="0" value="0">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="target">Open In</label>
                    <select name="target" id="target" class="form-control">
                        <option value="_self">Same tab</option>
                        <option value="_blank">New tab</option>
                    </select>
                </div>
                <label class="form-checkbox" style="align-self:end;margin-bottom:0.75rem;"><input type="checkbox" name="is_active" id="is_active" value="1" checked> <span>Active</span></label>
            </div>
            <div class="admin-card-actions" style="justify-content:flex-end;">
                <button type="button" class="btn btn-outline" onclick="closeMenuModal()">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Menu Item</button>
            </div>
        </form>
    </div>
</div>

<script>
function openMenuModal() {
    const form = document.getElementById('menuForm');
    form.reset();
    form.action = "{{ route('super_admin.navigation.store') }}";
    document.getElementById('menuModalTitle').textContent = 'Add Menu Item';
    document.getElementById('is_active').checked = true;
    document.getElementById('order').value = 0;
    toggleMenuDestination();
    document.getElementById('menuModal').classList.add('active');
}

function editMenuItem(item) {
    const form = document.getElementById('menuForm');
    form.action = `/super-admin/navigation-menu/${item.id}/update`;
    document.getElementById('menuModalTitle').textContent = 'Edit Menu Item';
    document.getElementById('label').value = item.label || '';
    document.getElementById('type').value = item.type || 'route';
    document.getElementById('route_name').value = item.route_name || '';
    document.getElementById('page_id').value = item.page_id || '';
    document.getElementById('url').value = item.url || '';
    document.getElementById('target').value = item.target || '_self';
    document.getElementById('order').value = item.order || 0;
    document.getElementById('is_active').checked = Boolean(item.is_active);
    toggleMenuDestination();
    document.getElementById('menuModal').classList.add('active');
}

function closeMenuModal() {
    document.getElementById('menuModal').classList.remove('active');
}

function toggleMenuDestination() {
    const type = document.getElementById('type').value;
    document.querySelectorAll('.menu-destination').forEach(el => el.style.display = 'none');
    const target = document.querySelector('.menu-' + type);
    if (target) target.style.display = 'block';
}
</script>
@endsection
