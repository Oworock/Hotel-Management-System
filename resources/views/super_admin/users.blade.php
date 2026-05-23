@extends('layouts.app')

@section('title', 'User Accounts')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;" class="animate-fade-in">
    <h2 style="font-size: 1.5rem; color: var(--text-primary);">All Registered Accounts</h2>
    <button class="btn btn-primary" onclick="openModal()">
        <i class="fa-solid fa-user-plus"></i> Create User Account
    </button>
</div>

<div class="glass-panel animate-fade-in" style="padding: 0;">
    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email Address</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Registered</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td style="font-family: monospace; font-weight: 600;">#{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @switch($user->role)
                                @case('super_admin')
                                    <span class="badge badge-danger">Super Admin</span>
                                    @break
                                @case('admin')
                                    <span class="badge badge-info">Admin</span>
                                    @break
                                @case('receptionist')
                                    <span class="badge badge-primary">Receptionist</span>
                                    @break
                                @case('kitchen_manager')
                                    <span class="badge badge-warning" style="background: rgba(245, 158, 11, 0.2); color: #f59e0b;">Kitchen Manager</span>
                                    @break
                                @case('tuck_shop_manager')
                                    <span class="badge badge-info" style="background: rgba(16, 185, 129, 0.2); color: #10b981;">Tuck Shop Manager</span>
                                    @break
                                @case('restaurant_manager')
                                    <span class="badge badge-primary" style="background: rgba(99, 102, 241, 0.2); color: #6366f1;">Restaurant Manager</span>
                                    @break
                                @case('staff')
                                    <span class="badge badge-success">Staff</span>
                                    @break
                                @case('customer')
                                    <span class="badge badge-warning">Guest</span>
                                    @break
                                @default
                                    <span class="badge badge-outline" style="border: 1px solid var(--border-color); color: var(--text-secondary);">{{ ucwords(str_replace('_', ' ', $user->role)) }}</span>
                            @endswitch
                            @if(Schema::hasTable('hotels') && $user->hotel_id && $user->hotel)
                                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem; display: flex; align-items: center; gap: 0.25rem;">
                                    <i class="fa-solid fa-hotel" style="font-size: 0.7rem; color: var(--primary);"></i>
                                    {{ $user->hotel->name }}
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($user->status === 'active')
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td style="color: var(--text-secondary); font-size: 0.85rem;">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>
                        <td style="text-align: right;">
                            <button type="button" class="btn btn-outline" style="font-size: 0.8rem; padding: 0.4rem 0.8rem; margin-right: 0.25rem;" onclick="openEditModal({{ json_encode(['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'role' => $user->role, 'functions' => $user->functions ?? [], 'hotel_id' => $user->hotel_id]) }})">
                                <i class="fa-solid fa-user-pen" style="color: var(--primary);"></i> Edit
                            </button>
                            @if($user->id !== auth()->user()->id)
                                <form action="{{ route('super_admin.users.impersonate', $user->id) }}" method="POST" style="display: inline-block; margin-right: 0.25rem;">
                                    @csrf
                                    <button type="submit" class="btn btn-outline" style="font-size: 0.8rem; padding: 0.4rem 0.8rem; border-color: var(--secondary); color: var(--secondary);">
                                        <i class="fa-solid fa-user-secret"></i> Impersonate
                                    </button>
                                </form>
                                <form action="{{ route('super_admin.users.toggle', $user->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-outline" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">
                                        @if($user->status === 'active')
                                            <i class="fa-solid fa-ban" style="color: var(--danger);"></i> Deactivate
                                        @else
                                            <i class="fa-solid fa-circle-check" style="color: var(--success);"></i> Activate
                                        @endif
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    @if($users->hasPages())
        <div style="padding: 1.5rem; display: flex; justify-content: center; border-top: 1px solid var(--border-color);">
            {{ $users->links() }}
        </div>
    @endif
</div>

<!-- Modal for User Creation -->
<div class="modal" id="createUserModal">
    <div class="modal-content glass-panel">
        <div class="modal-header">
            <h3 style="font-size: 1.25rem;"><i class="fa-solid fa-user-plus"></i> Create New Account</h3>
            <button class="theme-toggle" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <form action="{{ route('super_admin.users.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="modal-name" class="form-label">Full Name</label>
                <input type="text" name="name" id="modal-name" class="form-control" required placeholder="John Watson">
            </div>
            
            <div class="form-group">
                <label for="modal-email" class="form-label">Email Address</label>
                <input type="email" name="email" id="modal-email" class="form-control" required placeholder="john@hotel.com">
            </div>
            
            <div class="form-group">
                <label for="modal-password" class="form-label">Temporary Password</label>
                <input type="password" name="password" id="modal-password" class="form-control" required placeholder="Minimum 8 characters">
            </div>
            
            <div class="form-group">
                <label for="modal-role" class="form-label">Access Role</label>
                <select id="modal-role" class="form-control form-select" required onchange="toggleCustomRoleInput('create')">
                    <option value="receptionist">Receptionist (Reservations & Check-in/out)</option>
                    <option value="kitchen_manager">Kitchen Manager (Restaurant Orders)</option>
                    <option value="tuck_shop_manager">Tuck Shop Manager</option>
                    <option value="restaurant_manager">Restaurant Manager</option>
                    <option value="staff">Staff (Operational & Housekeeping)</option>
                    <option value="admin">Admin (Business Operations)</option>
                    <option value="super_admin">Super Admin (System Settings)</option>
                    <option value="customer">Guest (Booking & Check-in)</option>
                    <option value="custom">Custom Role...</option>
                </select>
                <input type="hidden" name="role" id="modal-role-hidden" value="receptionist">
            </div>
            
            @if(Schema::hasTable('hotels') && count($hotels) > 0)
                <div class="form-group">
                    <label for="modal-hotel" class="form-label">Assign Property (Hotel)</label>
                    <select name="hotel_id" id="modal-hotel" class="form-control form-select">
                        <option value="">-- No Specific Hotel (Global/Default) --</option>
                        @foreach($hotels as $h)
                            <option value="{{ $h->id }}">{{ $h->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="form-group" id="custom-role-group" style="display: none;">
                <label for="modal-custom-role" class="form-label">Custom Role Name</label>
                <input type="text" id="modal-custom-role" class="form-control" placeholder="e.g. laundry_man">
            </div>

            <div class="form-group" style="margin-top: 1.5rem;">
                <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">Assigned Functions (Permissions)</label>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; background: rgba(255,255,255,0.03); padding: 1rem; border-radius: 0.5rem; border: 1px solid var(--border-color);">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-secondary); font-size: 0.85rem;">
                        <input type="checkbox" name="functions[]" value="manage_rooms" class="create-func-checkbox">
                        Manage Rooms
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-secondary); font-size: 0.85rem;">
                        <input type="checkbox" name="functions[]" value="manage_bookings" class="create-func-checkbox">
                        Manage Bookings
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-secondary); font-size: 0.85rem;">
                        <input type="checkbox" name="functions[]" value="manage_guests" class="create-func-checkbox">
                        Manage Guests
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-secondary); font-size: 0.85rem;">
                        <input type="checkbox" name="functions[]" value="manage_payments" class="create-func-checkbox">
                        Manage Payments
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-secondary); font-size: 0.85rem;">
                        <input type="checkbox" name="functions[]" value="manage_reports" class="create-func-checkbox">
                        Manage Reports
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-secondary); font-size: 0.85rem;">
                        <input type="checkbox" name="functions[]" value="manage_users" class="create-func-checkbox">
                        Manage Users
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-secondary); font-size: 0.85rem;">
                        <input type="checkbox" name="functions[]" value="manage_settings" class="create-func-checkbox">
                        Manage Settings
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-secondary); font-size: 0.85rem;">
                        <input type="checkbox" name="functions[]" value="manage_tuck_shop" class="create-func-checkbox">
                        Manage Tuck Shop
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-secondary); font-size: 0.85rem;">
                        <input type="checkbox" name="functions[]" value="manage_restaurant" class="create-func-checkbox">
                        Manage Restaurant
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-secondary); font-size: 0.85rem;">
                        <input type="checkbox" name="functions[]" value="kitchen_dashboard" class="create-func-checkbox">
                        Kitchen Dashboard
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-secondary); font-size: 0.85rem;">
                        <input type="checkbox" name="functions[]" value="manage_channel_manager" class="create-func-checkbox">
                        Manage Channel Manager
                    </label>
                </div>
            </div>
            
            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                <button type="button" class="btn btn-outline" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Create User</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal for User Editing -->
<div class="modal" id="editUserModal">
    <div class="modal-content glass-panel">
        <div class="modal-header">
            <h3 style="font-size: 1.25rem;"><i class="fa-solid fa-user-pen"></i> Edit User Account</h3>
            <button class="theme-toggle" onclick="closeEditModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <form id="editUserForm" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="edit-modal-name" class="form-label">Full Name</label>
                <input type="text" name="name" id="edit-modal-name" class="form-control" required placeholder="John Watson">
            </div>
            
            <div class="form-group">
                <label for="edit-modal-email" class="form-label">Email Address</label>
                <input type="email" name="email" id="edit-modal-email" class="form-control" required placeholder="john@hotel.com">
            </div>
            
            <div class="form-group">
                <label for="edit-modal-password" class="form-label">New Password</label>
                <input type="password" name="password" id="edit-modal-password" class="form-control" placeholder="Leave blank to keep current password">
            </div>
            
            <div class="form-group">
                <label for="edit-modal-role" class="form-label">Access Role</label>
                <select id="edit-modal-role" class="form-control form-select" required onchange="toggleCustomRoleInput('edit')">
                    <option value="receptionist">Receptionist (Reservations & Check-in/out)</option>
                    <option value="kitchen_manager">Kitchen Manager (Restaurant Orders)</option>
                    <option value="tuck_shop_manager">Tuck Shop Manager</option>
                    <option value="restaurant_manager">Restaurant Manager</option>
                    <option value="staff">Staff (Operational & Housekeeping)</option>
                    <option value="admin">Admin (Business Operations)</option>
                    <option value="super_admin">Super Admin (System Settings)</option>
                    <option value="customer">Guest (Booking & Check-in)</option>
                    <option value="custom">Custom Role...</option>
                </select>
                <input type="hidden" name="role" id="edit-modal-role-hidden">
                <span id="role-warning" style="display: none; font-size: 0.75rem; color: var(--danger); margin-top: 0.25rem;">You cannot change your own role.</span>
            </div>

            @if(Schema::hasTable('hotels') && count($hotels) > 0)
                <div class="form-group">
                    <label for="edit-modal-hotel" class="form-label">Assign Property (Hotel)</label>
                    <select name="hotel_id" id="edit-modal-hotel" class="form-control form-select">
                        <option value="">-- No Specific Hotel (Global/Default) --</option>
                        @foreach($hotels as $h)
                            <option value="{{ $h->id }}">{{ $h->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="form-group" id="edit-custom-role-group" style="display: none;">
                <label for="edit-modal-custom-role" class="form-label">Custom Role Name</label>
                <input type="text" id="edit-modal-custom-role" class="form-control" placeholder="e.g. laundry_man">
            </div>

            <div class="form-group" style="margin-top: 1.5rem;">
                <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">Assigned Functions (Permissions)</label>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; background: rgba(255,255,255,0.03); padding: 1rem; border-radius: 0.5rem; border: 1px solid var(--border-color);">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-secondary); font-size: 0.85rem;">
                        <input type="checkbox" name="functions[]" value="manage_rooms" class="edit-func-checkbox">
                        Manage Rooms
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-secondary); font-size: 0.85rem;">
                        <input type="checkbox" name="functions[]" value="manage_bookings" class="edit-func-checkbox">
                        Manage Bookings
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-secondary); font-size: 0.85rem;">
                        <input type="checkbox" name="functions[]" value="manage_guests" class="edit-func-checkbox">
                        Manage Guests
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-secondary); font-size: 0.85rem;">
                        <input type="checkbox" name="functions[]" value="manage_payments" class="edit-func-checkbox">
                        Manage Payments
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-secondary); font-size: 0.85rem;">
                        <input type="checkbox" name="functions[]" value="manage_reports" class="edit-func-checkbox">
                        Manage Reports
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-secondary); font-size: 0.85rem;">
                        <input type="checkbox" name="functions[]" value="manage_users" class="edit-func-checkbox">
                        Manage Users
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-secondary); font-size: 0.85rem;">
                        <input type="checkbox" name="functions[]" value="manage_settings" class="edit-func-checkbox">
                        Manage Settings
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-secondary); font-size: 0.85rem;">
                        <input type="checkbox" name="functions[]" value="manage_tuck_shop" class="edit-func-checkbox">
                        Manage Tuck Shop
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-secondary); font-size: 0.85rem;">
                        <input type="checkbox" name="functions[]" value="manage_restaurant" class="edit-func-checkbox">
                        Manage Restaurant
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-secondary); font-size: 0.85rem;">
                        <input type="checkbox" name="functions[]" value="kitchen_dashboard" class="edit-func-checkbox">
                        Kitchen Dashboard
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-secondary); font-size: 0.85rem;">
                        <input type="checkbox" name="functions[]" value="manage_channel_manager" class="edit-func-checkbox">
                        Manage Channel Manager
                    </label>
                </div>
            </div>
            
            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                <button type="button" class="btn btn-outline" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const modal = document.getElementById('createUserModal');
    
    function openModal() {
        document.getElementById('modal-name').value = '';
        document.getElementById('modal-email').value = '';
        document.getElementById('modal-password').value = '';
        document.getElementById('modal-role').value = 'receptionist';
        document.getElementById('modal-role-hidden').value = 'receptionist';
        document.getElementById('modal-custom-role').value = '';
        document.getElementById('custom-role-group').style.display = 'none';
        if (document.getElementById('modal-hotel')) {
            document.getElementById('modal-hotel').value = '';
        }
        
        const checkboxes = document.querySelectorAll('.create-func-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = false;
        });

        modal.classList.add('active');
    }
    
    function closeModal() {
        modal.classList.remove('active');
    }
    
    modal.addEventListener('click', function(e) {
        if(e.target === modal) {
            closeModal();
        }
    });

    const editModal = document.getElementById('editUserModal');
    const editForm = document.getElementById('editUserForm');
    const editNameInput = document.getElementById('edit-modal-name');
    const editEmailInput = document.getElementById('edit-modal-email');
    const editRoleSelect = document.getElementById('edit-modal-role');
    const roleWarning = document.getElementById('role-warning');
    const currentUserId = {{ auth()->user()->id }};
    
    function openEditModal(user) {
        editForm.action = `/super-admin/users/${user.id}/update`;
        editNameInput.value = user.name;
        editEmailInput.value = user.email;
        document.getElementById('edit-modal-password').value = '';
        if (document.getElementById('edit-modal-hotel')) {
            document.getElementById('edit-modal-hotel').value = user.hotel_id || '';
        }
        
        const roles = ['receptionist', 'kitchen_manager', 'tuck_shop_manager', 'restaurant_manager', 'staff', 'admin', 'super_admin', 'customer'];
        if (roles.includes(user.role)) {
            editRoleSelect.value = user.role;
            document.getElementById('edit-custom-role-group').style.display = 'none';
            document.getElementById('edit-modal-custom-role').value = '';
            document.getElementById('edit-modal-role-hidden').value = user.role;
        } else {
            editRoleSelect.value = 'custom';
            document.getElementById('edit-custom-role-group').style.display = 'block';
            document.getElementById('edit-modal-custom-role').value = user.role;
            document.getElementById('edit-modal-role-hidden').value = user.role;
        }
        
        const checkboxes = document.querySelectorAll('.edit-func-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = false;
        });
        
        if (user.functions && Array.isArray(user.functions)) {
            user.functions.forEach(func => {
                const cb = document.querySelector(`.edit-func-checkbox[value="${func}"]`);
                if (cb) {
                    cb.checked = true;
                }
            });
        }
        
        if (user.id === currentUserId) {
            editRoleSelect.disabled = true;
            roleWarning.style.display = 'block';
        } else {
            editRoleSelect.disabled = false;
            roleWarning.style.display = 'none';
        }
        
        editModal.classList.add('active');
    }
    
    function closeEditModal() {
        editModal.classList.remove('active');
    }
    
    editModal.addEventListener('click', function(e) {
        if(e.target === editModal) {
            closeEditModal();
        }
    });

    function toggleCustomRoleInput(type) {
        const selectId = type === 'create' ? 'modal-role' : 'edit-modal-role';
        const inputGroupId = type === 'create' ? 'custom-role-group' : 'edit-custom-role-group';
        const customInputId = type === 'create' ? 'modal-custom-role' : 'edit-modal-custom-role';
        const hiddenId = type === 'create' ? 'modal-role-hidden' : 'edit-modal-role-hidden';
        
        const select = document.getElementById(selectId);
        const inputGroup = document.getElementById(inputGroupId);
        const customInput = document.getElementById(customInputId);
        const hiddenInput = document.getElementById(hiddenId);
        
        if (select.value === 'custom') {
            inputGroup.style.display = 'block';
            customInput.required = true;
            hiddenInput.value = customInput.value;
        } else {
            inputGroup.style.display = 'none';
            customInput.required = false;
            hiddenInput.value = select.value;
        }
    }

    document.getElementById('modal-custom-role').addEventListener('input', function() {
        if (document.getElementById('modal-role').value === 'custom') {
            document.getElementById('modal-role-hidden').value = this.value;
        }
    });
    document.getElementById('edit-modal-custom-role').addEventListener('input', function() {
        if (document.getElementById('edit-modal-role').value === 'custom') {
            document.getElementById('edit-modal-role-hidden').value = this.value;
        }
    });
</script>
@endsection
