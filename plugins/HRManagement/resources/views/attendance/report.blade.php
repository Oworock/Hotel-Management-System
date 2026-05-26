@extends('layouts.app')

@section('title', 'Attendance Report')

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 1.5rem 2rem 2.5rem 2rem;">
    <!-- Header -->
    <div style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;">
            <i class="fa-solid fa-chart-bar" style="color: var(--primary); margin-right: 0.5rem;"></i> Attendance Report
        </h2>
    </div>

    <!-- Filter -->
    <div class="glass-panel" style="padding: 1.5rem; margin-bottom: 1.5rem; border-radius: var(--radius-md);">
        <form method="GET" style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 1rem;">
            <input type="date" name="from_date" value="{{ request('from_date') }}" placeholder="From Date" style="padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">
            <input type="date" name="to_date" value="{{ request('to_date') }}" placeholder="To Date" style="padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">
            <select name="employee_id" style="padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">
                <option value="">All Employees</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>{{ $emp->full_name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-outline" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <i class="fa-solid fa-refresh"></i> Generate
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
                    <th>Check In</th>
                    <th>Check Out</th>
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
                        <td>{{ $record->check_in ? $record->check_in->format('H:i A') : 'N/A' }}</td>
                        <td>{{ $record->check_out ? $record->check_out->format('H:i A') : 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No attendance records found</td>
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
