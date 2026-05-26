@extends('layouts.app')

@section('title', $employee->full_name)

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 1.5rem 2rem 2.5rem 2rem;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;">{{ $employee->full_name }}</h2>
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.25rem;">{{ $employee->designation->name }} • {{ $employee->department->name }}</p>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="{{ route('admin.hr.employees.edit', $employee) }}" class="btn" style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: #fff; border: none; text-decoration: none; padding: 0.75rem 1.5rem; border-radius: var(--radius-md); font-weight: 600; cursor: pointer;">
                <i class="fa-solid fa-edit"></i> Edit Profile
            </a>
            <a href="{{ route('admin.hr.employees.index') }}" class="btn" style="display: inline-flex; align-items: center; gap: 0.5rem; background: var(--bg-secondary); color: var(--text-primary); border: 1px solid var(--border-color); text-decoration: none; padding: 0.75rem 1.5rem; border-radius: var(--radius-md); font-weight: 600; cursor: pointer;">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <!-- Left Column -->
        <div style="display: flex; flex-direction: column; gap: 2rem;">
            <!-- Personal Information -->
            <div class="glass-panel" style="padding: 1.5rem; border-radius: var(--radius-md);">
                <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1.5rem; margin-top: 0;">Personal Information</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 0.25rem;">Employee Code</label>
                        <p style="font-size: 1rem; color: var(--text-primary); margin: 0; font-weight: 600;">{{ $employee->employee_code }}</p>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 0.25rem;">Email</label>
                        <p style="font-size: 1rem; color: var(--text-primary); margin: 0; font-weight: 600;">{{ $employee->email }}</p>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 0.25rem;">Phone</label>
                        <p style="font-size: 1rem; color: var(--text-primary); margin: 0; font-weight: 600;">{{ $employee->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 0.25rem;">Date of Birth</label>
                        <p style="font-size: 1rem; color: var(--text-primary); margin: 0; font-weight: 600;">{{ $employee->dob ? $employee->dob->format('M d, Y') : 'N/A' }}</p>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 0.25rem;">Hired Date</label>
                        <p style="font-size: 1rem; color: var(--text-primary); margin: 0; font-weight: 600;">{{ $employee->hired_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 0.25rem;">Employment Type</label>
                        <p style="font-size: 1rem; color: var(--text-primary); margin: 0; font-weight: 600;">{{ ucfirst($employee->employment_type) }}</p>
                    </div>
                </div>
            </div>

            <!-- Compensation -->
            <div class="glass-panel" style="padding: 1.5rem; border-radius: var(--radius-md);">
                <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1.5rem; margin-top: 0;">Compensation</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 0.25rem;">Basic Salary</label>
                        <p style="font-size: 1rem; color: var(--primary); margin: 0; font-weight: 600;">${{ number_format($employee->basic_salary, 2) }}</p>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 0.25rem;">Allowances</label>
                        <p style="font-size: 1rem; color: var(--text-primary); margin: 0; font-weight: 600;">${{ number_format($employee->allowances, 2) }}</p>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 0.25rem;">Deductions</label>
                        <p style="font-size: 1rem; color: var(--text-primary); margin: 0; font-weight: 600;">${{ number_format($employee->deductions, 2) }}</p>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 0.25rem;">Address</label>
                        <p style="font-size: 1rem; color: var(--text-primary); margin: 0; font-weight: 600;">{{ $employee->address ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="glass-panel" style="padding: 1.5rem; border-radius: var(--radius-md);">
                <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1.5rem; margin-top: 0;">Recent Activities</h3>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @if($employee->attendance->count())
                        <div>
                            <label style="display: block; font-size: 0.75rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 0.25rem;">Last Attendance</label>
                            <p style="font-size: 1rem; color: var(--text-primary); margin: 0; font-weight: 600;">{{ $employee->getTodayAttendance()?->status ?? 'No attendance today' }}</p>
                        </div>
                    @endif
                    @if($employee->leaves->where('status', 'approved')->count())
                        <div>
                            <label style="display: block; font-size: 0.75rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 0.25rem;">Active Leaves</label>
                            <p style="font-size: 1rem; color: var(--text-primary); margin: 0; font-weight: 600;">{{ $employee->leaves->where('status', 'approved')->count() }} approved leave(s)</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div style="display: flex; flex-direction: column; gap: 2rem;">
            <!-- Status -->
            <div class="glass-panel" style="padding: 1.5rem; border-radius: var(--radius-md);">
                <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1.5rem; margin-top: 0;">Status</h3>
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 0.5rem;">Employment Status</label>
                        <span style="display: inline-block; padding: 0.5rem 1rem; border-radius: var(--radius-md); font-weight: 600; font-size: 0.875rem; @if($employee->status === 'active') background: rgba(16, 185, 129, 0.1); color: #10b981; @elseif($employee->status === 'inactive') background: rgba(245, 158, 11, 0.1); color: #f59e0b; @else background: rgba(239, 68, 68, 0.1); color: #ef4444; @endif">
                            {{ ucfirst($employee->status) }}
                        </span>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 0.25rem;">Department</label>
                        <p style="font-size: 1rem; color: var(--text-primary); margin: 0; font-weight: 600;">{{ $employee->department->name }}</p>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 0.25rem;">Emergency Contact</label>
                        <p style="font-size: 1rem; color: var(--text-primary); margin: 0; font-weight: 600;">{{ $employee->emergency_contact ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="glass-panel" style="padding: 1.5rem; border-radius: var(--radius-md);">
                <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1.5rem; margin-top: 0;">Statistics</h3>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: var(--bg-secondary); border-radius: var(--radius-sm);">
                        <p style="color: var(--text-secondary); margin: 0;">Attendance Records</p>
                        <span style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; padding: 0.5rem 1rem; border-radius: var(--radius-md); font-weight: 600; font-size: 0.875rem;">{{ $employee->attendance->count() }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: var(--bg-secondary); border-radius: var(--radius-sm);">
                        <p style="color: var(--text-secondary); margin: 0;">Leave Requests</p>
                        <span style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; padding: 0.5rem 1rem; border-radius: var(--radius-md); font-weight: 600; font-size: 0.875rem;">{{ $employee->leaves->count() }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: var(--bg-secondary); border-radius: var(--radius-sm);">
                        <p style="color: var(--text-secondary); margin: 0;">Performance Reviews</p>
                        <span style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 0.5rem 1rem; border-radius: var(--radius-md); font-weight: 600; font-size: 0.875rem;">{{ $employee->reviews->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
