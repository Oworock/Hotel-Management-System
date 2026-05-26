@extends('layouts.app')

@section('title', 'Mark Attendance')

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 1.5rem 2rem 2.5rem 2rem; max-width: 900px; margin: 0 auto;">
    <!-- Header -->
    <div style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;">
            <i class="fa-solid fa-clipboard-check" style="color: var(--primary); margin-right: 0.5rem;"></i> Mark Attendance
        </h2>
        <p style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.25rem;">Record attendance for employees</p>
    </div>

    <form method="POST" action="{{ route('admin.hr.attendance.store') }}">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">
            <div>
                <label for="employee_id" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Employee *</label>
                <select name="employee_id" id="employee_id" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);" required>
                    <option value="">Select Employee</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @if(in_array($emp->id, $todayAttendance)) disabled @endif>{{ $emp->full_name }}</option>
                    @endforeach
                </select>
                @error('employee_id')<p style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="date" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Date *</label>
                <input type="date" name="date" id="date" value="{{ now()->format('Y-m-d') }}" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);" required>
                @error('date')<p style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="status" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Status *</label>
                <select name="status" id="status" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);" required>
                    <option value="present">Present</option>
                    <option value="absent">Absent</option>
                    <option value="leave">On Leave</option>
                    <option value="half_day">Half Day</option>
                </select>
                @error('status')<p style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="notes" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Notes</label>
                <textarea name="notes" id="notes" rows="3" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);"></textarea>
                @error('notes')<p style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>
        </div>

        <div style="margin-top: 2rem; display: flex; gap: 1rem;">
            <button type="submit" class="btn" style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, var(--success), var(--primary-light)); color: #fff; border: none; padding: 0.75rem 1.5rem; border-radius: var(--radius-md); font-weight: 600; cursor: pointer;">
                <i class="fa-solid fa-check" style="font-size: 0.85rem;"></i>
                Mark Attendance
            </button>
            <a href="{{ route('admin.hr.attendance.index') }}" class="btn" style="display: inline-flex; align-items: center; gap: 0.5rem; background: var(--bg-secondary); color: var(--text-primary); border: 1px solid var(--border-color); text-decoration: none; padding: 0.75rem 1.5rem; border-radius: var(--radius-md); font-weight: 600; cursor: pointer;">
                <i class="fa-solid fa-list" style="font-size: 0.85rem;"></i>
                View Records
            </a>
        </div>
    </form>
</div>
@endsection
