@extends('layouts.app')

@section('title', isset($payroll) ? 'Edit Payroll Entry' : 'Create Payroll Entry')

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 1.5rem 2rem 2.5rem 2rem; max-width: 900px; margin: 0 auto;">
    <!-- Header -->
    <div style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;">
            <i class="fa-solid fa-{{ isset($payroll) ? 'edit' : 'plus' }}" style="color: var(--primary); margin-right: 0.5rem;"></i>
            {{ isset($payroll) ? 'Edit' : 'Create' }} Payroll Entry
        </h2>
    </div>

    @if($errors->any())
        <div style="padding: 1rem; background: rgba(239, 68, 68, 0.1); color: #ef4444; border-radius: var(--radius-md); margin-bottom: 1.5rem; border-left: 4px solid #ef4444;">
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach($errors->all() as $error)
                    <li style="font-size: 0.85rem;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ isset($payroll) ? route('admin.hr.payroll.update', $payroll) : route('admin.hr.payroll.store') }}" method="POST">
        @csrf
        @if(isset($payroll))
            @method('PUT')
        @endif

        <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">
            <div>
                <label for="employee_id" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Employee *</label>
                <select name="employee_id" id="employee_id" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);" required @if(isset($payroll)) disabled @endif>
                    <option value="">Select Employee</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @selected(old('employee_id', $payroll->employee_id ?? '') == $emp->id)>{{ $emp->full_name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div>
                    <label for="month" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Month *</label>
                    <select name="month" id="month" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);" required>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" @selected(old('month', $month ?? now()->month) == $i)>{{ \Carbon\Carbon::createFromFormat('m', $i)->format('F') }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label for="year" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Year *</label>
                    <input type="number" name="year" id="year" value="{{ old('year', $year ?? now()->year) }}" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);" required>
                </div>
            </div>

            <div>
                <label for="basic_salary" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Basic Salary *</label>
                <input type="number" name="basic_salary" id="basic_salary" value="{{ old('basic_salary', $payroll->basic_salary ?? '') }}" step="0.01" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);" required>
            </div>

            <div>
                <label for="allowances" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Allowances</label>
                <input type="number" name="allowances" id="allowances" value="{{ old('allowances', $payroll->allowances ?? 0) }}" step="0.01" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">
            </div>

            <div>
                <label for="deductions" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Deductions</label>
                <input type="number" name="deductions" id="deductions" value="{{ old('deductions', $payroll->deductions ?? 0) }}" step="0.01" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">
            </div>

            <div>
                <label for="tax" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Tax</label>
                <input type="number" name="tax" id="tax" value="{{ old('tax', $payroll->tax ?? 0) }}" step="0.01" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">
            </div>
        </div>

        <div style="margin-top: 2rem; display: flex; gap: 1rem;">
            <button type="submit" class="btn" style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, var(--info), var(--primary-light)); color: #fff; border: none; padding: 0.75rem 1.5rem; border-radius: var(--radius-md); font-weight: 600; cursor: pointer;">
                <i class="fa-solid fa-save" style="font-size: 0.85rem;"></i>
                {{ isset($payroll) ? 'Update' : 'Create' }} Payroll
            </button>
            <a href="{{ route('admin.hr.payroll.index') }}" class="btn" style="display: inline-flex; align-items: center; gap: 0.5rem; background: var(--bg-secondary); color: var(--text-primary); border: 1px solid var(--border-color); text-decoration: none; padding: 0.75rem 1.5rem; border-radius: var(--radius-md); font-weight: 600; cursor: pointer;">
                <i class="fa-solid fa-arrow-left" style="font-size: 0.85rem;"></i>
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
