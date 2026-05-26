@extends('layouts.app')

@section('title', isset($employee) ? 'Edit Employee' : 'Add Employee')

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 1.5rem 2rem 2.5rem 2rem; max-width: 900px; margin: 0 auto;">
    <!-- Header -->
    <div style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;">
            <i class="fa-solid fa-{{ isset($employee) ? 'edit' : 'user-plus' }}" style="color: var(--primary); margin-right: 0.5rem;"></i>
            {{ isset($employee) ? 'Edit' : 'Add New' }} Employee
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

    <form action="{{ isset($employee) ? route('admin.hr.employees.update', $employee) : route('admin.hr.employees.store') }}" method="POST">
        @csrf
        @if(isset($employee))
            @method('PUT')
        @endif

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div>
                <label for="employee_code" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Employee Code *</label>
                <input type="text" name="employee_code" id="employee_code" value="{{ old('employee_code', $employee->employee_code ?? '') }}" @if(isset($employee)) readonly @endif style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: @if(isset($employee)) var(--bg-secondary) @else var(--bg-primary) @endif; color: var(--text-primary);" placeholder="EMP001" required>
                @error('employee_code')<p style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Email *</label>
                <input type="email" name="email" id="email" value="{{ old('email', $employee->email ?? '') }}" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);" required>
                @error('email')<p style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="first_name" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">First Name *</label>
                <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $employee->first_name ?? '') }}" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);" required>
                @error('first_name')<p style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="last_name" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Last Name *</label>
                <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $employee->last_name ?? '') }}" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);" required>
                @error('last_name')<p style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="phone" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Phone</label>
                <input type="tel" name="phone" id="phone" value="{{ old('phone', $employee->phone ?? '') }}" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">
                @error('phone')<p style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="dob" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Date of Birth</label>
                <input type="date" name="dob" id="dob" value="{{ old('dob', $employee->dob ?? '') }}" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">
                @error('dob')<p style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="department_id" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Department *</label>
                <select name="department_id" id="department_id" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);" required>
                    <option value="">Select Department</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" @selected(old('department_id', $employee->department_id ?? '') == $dept->id)>{{ $dept->name }}</option>
                    @endforeach
                </select>
                @error('department_id')<p style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="designation_id" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Designation *</label>
                <select name="designation_id" id="designation_id" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);" required>
                    <option value="">Select Designation</option>
                    @foreach($designations as $desig)
                        <option value="{{ $desig->id }}" @selected(old('designation_id', $employee->designation_id ?? '') == $desig->id)>{{ $desig->name }}</option>
                    @endforeach
                </select>
                @error('designation_id')<p style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="hired_date" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Hired Date *</label>
                <input type="date" name="hired_date" id="hired_date" value="{{ old('hired_date', $employee->hired_date ?? '') }}" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);" required>
                @error('hired_date')<p style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="employment_type" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Employment Type *</label>
                <select name="employment_type" id="employment_type" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);" required>
                    <option value="full-time" @selected(old('employment_type', $employee->employment_type ?? 'full-time') == 'full-time')>Full-time</option>
                    <option value="part-time" @selected(old('employment_type', $employee->employment_type ?? '') == 'part-time')>Part-time</option>
                    <option value="contract" @selected(old('employment_type', $employee->employment_type ?? '') == 'contract')>Contract</option>
                </select>
                @error('employment_type')<p style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="basic_salary" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Basic Salary *</label>
                <input type="number" name="basic_salary" id="basic_salary" value="{{ old('basic_salary', $employee->basic_salary ?? '') }}" step="0.01" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);" required>
                @error('basic_salary')<p style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="status" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Status</label>
                <select name="status" id="status" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">
                    <option value="active" @selected(old('status', $employee->status ?? 'active') == 'active')>Active</option>
                    <option value="inactive" @selected(old('status', $employee->status ?? '') == 'inactive')>Inactive</option>
                    <option value="terminated" @selected(old('status', $employee->status ?? '') == 'terminated')>Terminated</option>
                </select>
            </div>

            <div style="grid-column: 1/-1;">
                <label for="address" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Address</label>
                <textarea name="address" id="address" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);" rows="3">{{ old('address', $employee->address ?? '') }}</textarea>
                @error('address')<p style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="emergency_contact" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Emergency Contact</label>
                <input type="text" name="emergency_contact" id="emergency_contact" value="{{ old('emergency_contact', $employee->emergency_contact ?? '') }}" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">
            </div>
        </div>

        <div style="margin-top: 2rem; display: flex; gap: 1rem;">
            <button type="submit" class="btn" style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: #fff; border: none; padding: 0.75rem 1.5rem; border-radius: var(--radius-md); font-weight: 600; cursor: pointer;">
                <i class="fa-solid fa-save" style="font-size: 0.85rem;"></i>
                {{ isset($employee) ? 'Update Employee' : 'Add Employee' }}
            </button>
            <a href="{{ route('admin.hr.employees.index') }}" class="btn" style="display: inline-flex; align-items: center; gap: 0.5rem; background: var(--bg-secondary); color: var(--text-primary); border: 1px solid var(--border-color); text-decoration: none; padding: 0.75rem 1.5rem; border-radius: var(--radius-md); font-weight: 600; cursor: pointer;">
                <i class="fa-solid fa-arrow-left" style="font-size: 0.85rem;"></i>
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
