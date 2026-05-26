@extends('layouts.app')

@section('title', 'Employees')

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 1.5rem 2rem 2.5rem 2rem;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;">
                <i class="fa-solid fa-users" style="color: var(--primary); margin-right: 0.5rem;"></i> Employees
            </h2>
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.25rem;">Manage your team members</p>
        </div>
        <a href="{{ route('admin.hr.employees.create') }}" class="btn" style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: #fff; border: none; text-decoration: none; padding: 0.75rem 1.5rem; border-radius: var(--radius-md); font-weight: 600; cursor: pointer;">
            <i class="fa-solid fa-user-plus"></i> Add Employee
        </a>
    </div>

    @if(session('success'))
        <div style="padding: 1rem; background: rgba(16, 185, 129, 0.1); color: #10b981; border-radius: var(--radius-md); margin-bottom: 1.5rem; border-left: 4px solid #10b981;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Search/Filter -->
    <div class="glass-panel" style="padding: 1.5rem; margin-bottom: 1.5rem; border-radius: var(--radius-md);">
        <form method="GET" style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 1rem;">
            <input type="text" name="search" placeholder="Search by name or email..." value="{{ request('search') }}" style="padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">
            <select name="department" style="padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" @selected(request('department') == $dept->id)>{{ $dept->name }}</option>
                @endforeach
            </select>
            <select name="status" style="padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">
                <option value="">All Status</option>
                <option value="active" @selected(request('status') == 'active')>Active</option>
                <option value="inactive" @selected(request('status') == 'inactive')>Inactive</option>
                <option value="terminated" @selected(request('status') == 'terminated')>Terminated</option>
            </select>
            <button type="submit" class="btn btn-outline" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <i class="fa-solid fa-search"></i> Search
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="table-container" style="border-radius: var(--radius-md); overflow: hidden;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Status</th>
                    <th style="text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                    <tr>
                        <td><strong>{{ $employee->full_name }}</strong></td>
                        <td>{{ $employee->email }}</td>
                        <td>{{ $employee->department->name }}</td>
                        <td>{{ $employee->designation->name }}</td>
                        <td>
                            <span style="padding: 0.25rem 0.75rem; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 600; @if($employee->status === 'active') background: rgba(16, 185, 129, 0.1); color: #10b981; @elseif($employee->status === 'inactive') background: rgba(245, 158, 11, 0.1); color: #f59e0b; @else background: rgba(239, 68, 68, 0.1); color: #ef4444; @endif">
                                {{ ucfirst($employee->status) }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 0.5rem; justify-content: center; font-size: 0.85rem;">
                                <a href="{{ route('admin.hr.employees.show', $employee) }}" style="color: var(--primary); text-decoration: none; font-weight: 600;">View</a>
                                <a href="{{ route('admin.hr.employees.edit', $employee) }}" style="color: var(--success); text-decoration: none; font-weight: 600;">Edit</a>
                                <form method="POST" action="{{ route('admin.hr.employees.destroy', $employee) }}" style="display: inline;" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: none; border: none; color: var(--danger); text-decoration: none; font-weight: 600; cursor: pointer;">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 2rem;">
                            No employees found. <a href="{{ route('admin.hr.employees.create') }}" style="color: var(--primary); text-decoration: none; font-weight: 600;">Create one</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="margin-top: 1.5rem;">
        {{ $employees->links() }}
    </div>
</div>
@endsection
