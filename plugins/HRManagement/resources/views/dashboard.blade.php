@extends('layouts.app')

@section('title', 'HR Management Dashboard')

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 1.5rem 2rem 2.5rem 2rem;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;">
                <i class="fa-solid fa-chart-pie" style="color: var(--primary); margin-right: 0.5rem;"></i> HR Management Dashboard
            </h2>
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.25rem;">Manage your workforce efficiently</p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="glass-panel stat-card" style="display: flex; align-items: center; justify-content: space-between; padding: 1.5rem; border-radius: var(--radius-md); box-shadow: var(--shadow-sm);">
            <div>
                <p style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">Total Employees</p>
                <div class="stat-value" style="color: var(--primary); font-size: 1.8rem; font-weight: 700; margin-top: 0.5rem;">{{ $totalEmployees }}</div>
            </div>
            <div class="stat-icon" style="font-size: 2rem; color: var(--primary); opacity: 0.85;"><i class="fa-solid fa-users"></i></div>
        </div>

        <div class="glass-panel stat-card" style="display: flex; align-items: center; justify-content: space-between; padding: 1.5rem; border-radius: var(--radius-md); box-shadow: var(--shadow-sm);">
            <div>
                <p style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">Active Employees</p>
                <div class="stat-value" style="color: var(--success); font-size: 1.8rem; font-weight: 700; margin-top: 0.5rem;">{{ $activeEmployees }}</div>
            </div>
            <div class="stat-icon" style="font-size: 2rem; color: var(--success); opacity: 0.85;"><i class="fa-solid fa-check-circle"></i></div>
        </div>

        <div class="glass-panel stat-card" style="display: flex; align-items: center; justify-content: space-between; padding: 1.5rem; border-radius: var(--radius-md); box-shadow: var(--shadow-sm);">
            <div>
                <p style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">Departments</p>
                <div class="stat-value" style="color: var(--text-primary); font-size: 1.8rem; font-weight: 700; margin-top: 0.5rem;">{{ $totalDepartments }}</div>
            </div>
            <div class="stat-icon" style="font-size: 2rem; color: var(--text-secondary); opacity: 0.85;"><i class="fa-solid fa-building"></i></div>
        </div>

        <div class="glass-panel stat-card" style="display: flex; align-items: center; justify-content: space-between; padding: 1.5rem; border-radius: var(--radius-md); box-shadow: var(--shadow-sm);">
            <div>
                <p style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">Pending Leaves</p>
                <div class="stat-value" style="color: var(--warning); font-size: 1.8rem; font-weight: 700; margin-top: 0.5rem;">{{ $pendingLeaves }}</div>
            </div>
            <div class="stat-icon" style="font-size: 2rem; color: var(--warning); opacity: 0.85;"><i class="fa-solid fa-umbrella"></i></div>
        </div>

        <div class="glass-panel stat-card" style="display: flex; align-items: center; justify-content: space-between; padding: 1.5rem; border-radius: var(--radius-md); box-shadow: var(--shadow-sm);">
            <div>
                <p style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">Absent Today</p>
                <div class="stat-value" style="color: var(--danger); font-size: 1.8rem; font-weight: 700; margin-top: 0.5rem;">{{ $absentToday }}</div>
            </div>
            <div class="stat-icon" style="font-size: 2rem; color: var(--danger); opacity: 0.85;"><i class="fa-solid fa-user-slash"></i></div>
        </div>

        <div class="glass-panel stat-card" style="display: flex; align-items: center; justify-content: space-between; padding: 1.5rem; border-radius: var(--radius-md); box-shadow: var(--shadow-sm);">
            <div>
                <p style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">On Leave Today</p>
                <div class="stat-value" style="color: var(--info); font-size: 1.8rem; font-weight: 700; margin-top: 0.5rem;">{{ $onLeaveToday }}</div>
            </div>
            <div class="stat-icon" style="font-size: 2rem; color: var(--info); opacity: 0.85;"><i class="fa-solid fa-plane"></i></div>
        </div>
    </div>

    <!-- Action Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <a href="{{ route('admin.hr.employees.create') }}" class="btn" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: #fff; border: none; text-decoration: none; padding: 0.75rem 1.5rem; border-radius: var(--radius-md); font-weight: 600; cursor: pointer;">
            <i class="fa-solid fa-user-plus"></i> Add Employee
        </a>
        <a href="{{ route('admin.hr.attendance.mark') }}" class="btn" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; background: linear-gradient(135deg, var(--success), var(--primary-light)); color: #fff; border: none; text-decoration: none; padding: 0.75rem 1.5rem; border-radius: var(--radius-md); font-weight: 600; cursor: pointer;">
            <i class="fa-solid fa-check"></i> Mark Attendance
        </a>
        <a href="{{ route('admin.hr.leaves.request') }}" class="btn" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; background: linear-gradient(135deg, var(--warning), var(--primary-light)); color: #fff; border: none; text-decoration: none; padding: 0.75rem 1.5rem; border-radius: var(--radius-md); font-weight: 600; cursor: pointer;">
            <i class="fa-solid fa-edit"></i> Request Leave
        </a>
        <a href="{{ route('admin.hr.payroll.create') }}" class="btn" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; background: linear-gradient(135deg, var(--info), var(--primary-light)); color: #fff; border: none; text-decoration: none; padding: 0.75rem 1.5rem; border-radius: var(--radius-md); font-weight: 600; cursor: pointer;">
            <i class="fa-solid fa-money-bill"></i> Create Payroll
        </a>
    </div>

    <!-- Recent Employees -->
    <div class="glass-panel" style="padding: 1.5rem; border-radius: var(--radius-md); display: flex; flex-direction: column;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.25rem; font-weight: 600; margin: 0; color: var(--text-primary);">
                <i class="fa-solid fa-users" style="color: var(--primary); margin-right: 0.5rem;"></i> Recent Employees
            </h3>
            <a href="{{ route('admin.hr.employees.index') }}" style="font-size: 0.85rem; color: var(--primary); font-weight: 600; text-decoration: none;">
                View All <i class="fa-solid fa-arrow-right" style="font-size: 0.75rem;"></i>
            </a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem;">
            @forelse($recentEmployees as $employee)
                <div style="padding: 1rem; background: var(--bg-secondary); border-radius: var(--radius-sm); border-left: 3px solid var(--primary);">
                    <p style="font-weight: 600; color: var(--text-primary); margin: 0;">{{ $employee->full_name }}</p>
                    <p style="font-size: 0.85rem; color: var(--text-secondary); margin: 0.25rem 0;">{{ $employee->designation->name }}</p>
                    <p style="font-size: 0.75rem; color: var(--text-secondary); margin: 0;">{{ $employee->department->name }}</p>
                </div>
            @empty
                <p style="color: var(--text-secondary); text-align: center; grid-column: 1/-1;">No employees yet</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
