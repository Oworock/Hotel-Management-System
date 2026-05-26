@extends('layouts.app')

@section('title', 'Staff Management')

@section('content')
<div style="display: flex; flex-direction: column; gap: 2rem;" class="animate-fade-in">
    <!-- Header and Navigation Tabs -->
    <div class="glass-panel" style="padding: 1.5rem 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="font-size: 1.5rem; margin-bottom: 0.25rem;"><i class="fa-solid fa-users" style="color: var(--primary); margin-right: 0.5rem;"></i> Staff & Security Operations</h2>
                <p style="color: var(--text-secondary); font-size: 0.875rem;">Manage employee credentials, track attendance, and analyze clocked shifts.</p>
            </div>
            <div>
                <button onclick="toggleCreateModal(true)" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-user-plus"></i> Add Staff Account
                </button>
            </div>
        </div>

        <div style="display: flex; gap: 1rem; border-bottom: 1px solid var(--border-color); margin-top: 1.5rem;">
            <button onclick="switchTab('staff')" id="tab-btn-staff" class="tab-btn active-tab" style="background: none; border: none; padding: 0.75rem 1rem; font-family: inherit; font-size: 0.95rem; font-weight: 600; cursor: pointer; color: var(--primary); border-bottom: 2px solid var(--primary);">
                Staff Directory
            </button>
            <button onclick="switchTab('shifts')" id="tab-btn-shifts" class="tab-btn" style="background: none; border: none; padding: 0.75rem 1rem; font-family: inherit; font-size: 0.95rem; font-weight: 600; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent;">
                Shift Tracking
            </button>
        </div>
    </div>

    <!-- Staff Directory Tab Content -->
    <div id="tab-content-staff" class="tab-content">
        <div class="glass-panel" style="padding: 0;">
            <div class="table-container" style="border: none; border-radius: var(--radius-md); overflow: hidden;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Staff ID</th>
                            <th>Name / Email</th>
                            <th>Status</th>
                            <th>Created Date</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $staff)
                            <tr>
                                <td style="font-weight: 700;">#{{ $staff->id }}</td>
                                <td>
                                    <div style="font-weight: 600;">{{ $staff->name }}</div>
                                    <div style="font-size: 0.8rem; color: var(--text-secondary);">{{ $staff->email }}</div>
                                    @if($staff->phone)
                                        <div style="font-size: 0.78rem; color: var(--text-muted);">{{ $staff->phone }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $staff->status === 'active' ? 'success' : 'danger' }}">
                                        {{ ucfirst($staff->status) }}
                                    </span>
                                </td>
                                <td style="font-size: 0.85rem; color: var(--text-secondary);">
                                    {{ $staff->created_at->format('Y-m-d') }}
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 0.5rem; justify-content: flex-end;">
                                        <button onclick="openEditModal({{ json_encode($staff) }})" class="btn btn-sm btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </button>
                                        <form action="{{ route('admin.users.toggle', $staff->id) }}" method="POST" style="margin: 0;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-{{ $staff->status === 'active' ? 'outline-danger' : 'success' }}" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">
                                                <i class="fa-solid fa-power-off"></i> {{ $staff->status === 'active' ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 4rem 0;">
                                    <i class="fa-solid fa-user-shield" style="font-size: 3rem; margin-bottom: 1rem; color: var(--border-color); display: block;"></i>
                                    <p>No staff accounts registered yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div style="padding: 1.5rem; display: flex; justify-content: center; border-top: 1px solid var(--border-color);">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Shifts Tab Content -->
    <div id="tab-content-shifts" class="tab-content" style="display: none;">
        <div class="glass-panel" style="padding: 0;">
            <div class="table-container" style="border: none; border-radius: var(--radius-md); overflow: hidden;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Employee Name</th>
                            <th>Email</th>
                            <th>Clocked In</th>
                            <th>Clocked Out</th>
                            <th>Duration (Hrs:Mins)</th>
                            <th>Shift Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shifts as $shift)
                            <tr>
                                <td style="font-weight: 600;">{{ $shift->user->name ?? 'Deleted Staff' }}</td>
                                <td>{{ $shift->user->email ?? 'N/A' }}</td>
                                <td style="font-size: 0.9rem;">
                                    {{ $shift->clock_in_at->format('M d, Y - h:i A') }}
                                </td>
                                <td style="font-size: 0.9rem;">
                                    @if($shift->clock_out_at)
                                        {{ $shift->clock_out_at->format('M d, Y - h:i A') }}
                                    @else
                                        <span style="color: var(--text-muted); font-style: italic;">N/A</span>
                                    @endif
                                </td>
                                <td style="font-family: monospace; font-weight: 600;">
                                    @if($shift->clock_out_at)
                                        @php
                                            $hours = floor($shift->duration_minutes / 60);
                                            $mins = $shift->duration_minutes % 60;
                                        @endphp
                                        {{ sprintf('%02d:%02d', $hours, $mins) }}
                                    @else
                                        <span style="color: var(--primary); font-weight: 700;">Ongoing</span>
                                    @endif
                                </td>
                                <td>
                                    @if($shift->clock_out_at)
                                        <span class="badge badge-success" style="opacity: 0.75;">Completed</span>
                                    @else
                                        <span class="badge badge-primary" style="animation: pulseGlow 2s infinite;">Active Now</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 4rem 0;">
                                    <i class="fa-regular fa-clock" style="font-size: 3rem; margin-bottom: 1rem; color: var(--border-color); display: block;"></i>
                                    <p>No shifts registered yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($shifts->hasPages())
                <div style="padding: 1.5rem; display: flex; justify-content: center; border-top: 1px solid var(--border-color);">
                    {{ $shifts->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal: Add Staff Account -->
<div id="create-modal" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.5); z-index: 10000; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
    <div class="glass-panel" style="width: 100%; max-width: 480px; padding: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.25rem;"><i class="fa-solid fa-user-plus" style="color: var(--primary); margin-right: 0.5rem;"></i> Register Staff</h3>
            <button onclick="toggleCreateModal(false)" style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: var(--text-secondary);"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" placeholder="Enter employee full name" required autocomplete="name">
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="Enter employee email" required autocomplete="email">
            </div>

            @include('partials.phone-input', ['field' => 'phone', 'label' => 'Mobile Number', 'style' => 'margin-bottom: 1rem;'])

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter password (min. 8 chars)" required autocomplete="new-password">
            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                <button type="button" onclick="toggleCreateModal(false)" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Account</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Staff Account -->
<div id="edit-modal" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.5); z-index: 10000; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
    <div class="glass-panel" style="width: 100%; max-width: 480px; padding: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.25rem;"><i class="fa-solid fa-user-pen" style="color: var(--primary); margin-right: 0.5rem;"></i> Edit Staff Member</h3>
            <button onclick="toggleEditModal(false)" style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: var(--text-secondary);"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form id="edit-form" method="POST">
            @csrf
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" id="edit-name" class="form-control" required autocomplete="name">
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" id="edit-email" class="form-control" required autocomplete="email">
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Mobile Number</label>
                <div style="display:grid;grid-template-columns:minmax(130px,0.55fr) minmax(0,1fr);gap:0.65rem;">
                    <select name="phone_country_code" id="edit-phone-country-code" class="form-control form-select">
                        @foreach(\App\Support\PhoneNumber::countries() as $code => $country)
                            <option value="{{ $code }}">{{ \App\Support\PhoneNumber::countrySelectLabel($code, $country) }}</option>
                        @endforeach
                    </select>
                    <input type="tel" name="phone" id="edit-phone" class="form-control" placeholder="8012345678" inputmode="tel">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">New Password (leave blank to keep current)</label>
                <input type="password" name="password" class="form-control" autocomplete="new-password">
            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                <button type="button" onclick="toggleEditModal(false)" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    function switchTab(tab) {
        const staffTabBtn = document.getElementById('tab-btn-staff');
        const shiftsTabBtn = document.getElementById('tab-btn-shifts');
        const staffContent = document.getElementById('tab-content-staff');
        const shiftsContent = document.getElementById('tab-content-shifts');

        if (tab === 'staff') {
            staffTabBtn.className = 'tab-btn active-tab';
            staffTabBtn.style.color = 'var(--primary)';
            staffTabBtn.style.borderBottomColor = 'var(--primary)';
            
            shiftsTabBtn.className = 'tab-btn';
            shiftsTabBtn.style.color = 'var(--text-secondary)';
            shiftsTabBtn.style.borderBottomColor = 'transparent';

            staffContent.style.display = 'block';
            shiftsContent.style.display = 'none';
        } else {
            shiftsTabBtn.className = 'tab-btn active-tab';
            shiftsTabBtn.style.color = 'var(--primary)';
            shiftsTabBtn.style.borderBottomColor = 'var(--primary)';
            
            staffTabBtn.className = 'tab-btn';
            staffTabBtn.style.color = 'var(--text-secondary)';
            staffTabBtn.style.borderBottomColor = 'transparent';

            shiftsContent.style.display = 'block';
            staffContent.style.display = 'none';
        }
    }

    function toggleCreateModal(show) {
        document.getElementById('create-modal').style.display = show ? 'flex' : 'none';
    }

    function toggleEditModal(show) {
        document.getElementById('edit-modal').style.display = show ? 'flex' : 'none';
    }

    function openEditModal(staff) {
        document.getElementById('edit-name').value = staff.name;
        document.getElementById('edit-email').value = staff.email;
        setPhoneFields('edit-phone-country-code', 'edit-phone', staff.phone || '');
        
        // Dynamic form action
        const actionUrl = `/admin/users/${staff.id}/update`;
        document.getElementById('edit-form').setAttribute('action', actionUrl);
        
        toggleEditModal(true);
    }

    function setPhoneFields(countryId, phoneId, phone) {
        const countries = @json(array_keys(\App\Support\PhoneNumber::countries()));
        const countrySelect = document.getElementById(countryId);
        const phoneInput = document.getElementById(phoneId);
        const digits = String(phone || '').replace(/\D/g, '');
        let selected = '+234';
        let national = digits.replace(/^0+/, '');

        countries.forEach(code => {
            const codeDigits = code.replace(/\D/g, '');
            if (digits.startsWith(codeDigits)) {
                selected = code;
                national = digits.slice(codeDigits.length);
            }
        });

        countrySelect.value = selected;
        phoneInput.value = national;
    }
</script>

<style>
    .tab-btn {
        transition: border-bottom var(--transition-fast), color var(--transition-fast);
    }
    .tab-btn:hover {
        color: var(--primary) !important;
    }
</style>
@endsection
