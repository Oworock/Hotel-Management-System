@extends('layouts.app')

@section('title', 'Departments')

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 1.5rem 2rem 2.5rem 2rem;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;">
                <i class="fa-solid fa-building" style="color: var(--primary); margin-right: 0.5rem;"></i> Departments
            </h2>
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.25rem;">Manage organization structure</p>
        </div>
        <a href="{{ route('admin.hr.departments.create') }}" class="btn" style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: #fff; border: none; text-decoration: none; padding: 0.75rem 1.5rem; border-radius: var(--radius-md); font-weight: 600; cursor: pointer;">
            <i class="fa-solid fa-plus"></i> Add Department
        </a>
    </div>

    @if(session('success'))
        <div style="padding: 1rem; background: rgba(16, 185, 129, 0.1); color: #10b981; border-radius: var(--radius-md); margin-bottom: 1.5rem; border-left: 4px solid #10b981;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Table -->
    <div class="table-container" style="border-radius: var(--radius-md); overflow: hidden;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Manager</th>
                    <th>Employees</th>
                    <th>Budget</th>
                    <th>Status</th>
                    <th style="text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($departments as $department)
                    <tr>
                        <td><strong>{{ $department->name }}</strong></td>
                        <td>{{ $department->manager ? $department->manager->full_name : 'N/A' }}</td>
                        <td>{{ $department->employees->count() }}</td>
                        <td>${{ number_format($department->budget ?? 0, 2) }}</td>
                        <td>
                            <span style="padding: 0.25rem 0.75rem; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 600; @if($department->is_active) background: rgba(16, 185, 129, 0.1); color: #10b981; @else background: rgba(107, 114, 128, 0.1); color: #6b7280; @endif">
                                {{ $department->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 0.5rem; justify-content: center; font-size: 0.85rem;">
                                <a href="{{ route('admin.hr.departments.edit', $department) }}" style="color: var(--primary); text-decoration: none; font-weight: 600;">Edit</a>
                                <form method="POST" action="{{ route('admin.hr.departments.destroy', $department) }}" style="display: inline;" onsubmit="return confirm('Are you sure?')">
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
                            No departments found. <a href="{{ route('admin.hr.departments.create') }}" style="color: var(--primary); text-decoration: none; font-weight: 600;">Create one</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="margin-top: 1.5rem;">
        {{ $departments->links() }}
    </div>
</div>
@endsection
