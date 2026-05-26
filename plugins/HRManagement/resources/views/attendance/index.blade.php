@extends('layouts.app')

@section('title', 'Attendance Records')

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 1.5rem 2rem 2.5rem 2rem;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;">
                <i class="fa-solid fa-clipboard-list" style="color: var(--primary); margin-right: 0.5rem;"></i> Attendance Records
            </h2>
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.25rem;">View and manage attendance</p>
        </div>
        <a href="{{ route('admin.hr.attendance.mark') }}" class="btn" style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, var(--success), var(--primary-light)); color: #fff; border: none; text-decoration: none; padding: 0.75rem 1.5rem; border-radius: var(--radius-md); font-weight: 600; cursor: pointer;">
            <i class="fa-solid fa-plus"></i> Mark Attendance
        </a>
    </div>

    <!-- Filter -->
    <div class="glass-panel" style="padding: 1.5rem; margin-bottom: 1.5rem; border-radius: var(--radius-md);">
        <form method="GET" style="display: grid; grid-template-columns: 1fr auto; gap: 1rem;">
            <input type="date" name="date" value="{{ $date }}" style="padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">
            <button type="submit" class="btn btn-outline" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <i class="fa-solid fa-filter"></i> Filter
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="table-container" style="border-radius: var(--radius-md); overflow: hidden;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Duration</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendance as $record)
                    <tr>
                        <td><strong>{{ $record->employee->full_name }}</strong></td>
                        <td>{{ $record->date->format('M d, Y') }}</td>
                        <td>
                            <span style="padding: 0.25rem 0.75rem; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 600; @if($record->status === 'present') background: rgba(16, 185, 129, 0.1); color: #10b981; @elseif($record->status === 'absent') background: rgba(239, 68, 68, 0.1); color: #ef4444; @elseif($record->status === 'leave') background: rgba(245, 158, 11, 0.1); color: #f59e0b; @else background: rgba(59, 130, 246, 0.1); color: #3b82f6; @endif">
                                {{ ucfirst($record->status) }}
                            </span>
                        </td>
                        <td>{{ $record->duration_minutes ? $record->duration_minutes . ' min' : 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No attendance records found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="margin-top: 1.5rem;">
        {{ $attendance->links() }}
    </div>
</div>
@endsection
