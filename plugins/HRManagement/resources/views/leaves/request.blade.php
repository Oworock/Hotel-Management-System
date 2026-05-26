@extends('layouts.app')

@section('title', 'Request Leave')

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 1.5rem 2rem 2.5rem 2rem; max-width: 900px; margin: 0 auto;">
    <!-- Header -->
    <div style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;">
            <i class="fa-solid fa-edit" style="color: var(--primary); margin-right: 0.5rem;"></i> Request Leave
        </h2>
    </div>

    @if($errors->any())
        <div style="padding: 1rem; background: rgba(239, 68, 68, 0.1); color: #ef4444; border-radius: var(--radius-md); margin-bottom: 1.5rem; border-left: 4px solid #ef4444;">
            <p style="font-weight: 600; margin: 0 0 0.5rem 0;">Please fix the following errors:</p>
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach($errors->all() as $error)
                    <li style="font-size: 0.85rem;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.hr.leaves.store') }}" method="POST">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">
            <div>
                <label for="employee_id" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Employee *</label>
                <select name="employee_id" id="employee_id" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);" required>
                    <option value="">Select Employee</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @selected(old('employee_id') == $emp->id)>{{ $emp->full_name }}</option>
                    @endforeach
                </select>
                @error('employee_id')<p style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="leave_type_id" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Leave Type *</label>
                <select name="leave_type_id" id="leave_type_id" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);" required>
                    <option value="">Select Leave Type</option>
                    @foreach($leaveTypes as $type)
                        <option value="{{ $type->id }}" @selected(old('leave_type_id') == $type->id)>{{ $type->name }} ({{ $type->days_per_year }} days/year)</option>
                    @endforeach
                </select>
                @error('leave_type_id')<p style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="from_date" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">From Date *</label>
                <input type="date" name="from_date" id="from_date" value="{{ old('from_date') }}" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);" required>
                @error('from_date')<p style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="to_date" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">To Date *</label>
                <input type="date" name="to_date" id="to_date" value="{{ old('to_date') }}" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);" required>
                @error('to_date')<p style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="reason" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Reason</label>
                <textarea name="reason" id="reason" rows="4" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);" placeholder="Enter reason for leave request">{{ old('reason') }}</textarea>
                @error('reason')<p style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>
        </div>

        <div style="margin-top: 2rem; display: flex; gap: 1rem;">
            <button type="submit" class="btn" style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, var(--warning), var(--primary-light)); color: #fff; border: none; padding: 0.75rem 1.5rem; border-radius: var(--radius-md); font-weight: 600; cursor: pointer;">
                <i class="fa-solid fa-paper-plane" style="font-size: 0.85rem;"></i>
                Submit Request
            </button>
            <a href="{{ route('admin.hr.leaves.index') }}" class="btn" style="display: inline-flex; align-items: center; gap: 0.5rem; background: var(--bg-secondary); color: var(--text-primary); border: 1px solid var(--border-color); text-decoration: none; padding: 0.75rem 1.5rem; border-radius: var(--radius-md); font-weight: 600; cursor: pointer;">
                <i class="fa-solid fa-arrow-left" style="font-size: 0.85rem;"></i>
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
