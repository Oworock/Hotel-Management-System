@extends('layouts.app')

@section('title', 'My Attendance')

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 1.5rem 2rem 2.5rem 2rem;">
    <!-- Header -->
    <div style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;">
            <i class="fa-solid fa-calendar-check" style="color: var(--primary); margin-right: 0.5rem;"></i> My Attendance
        </h2>
        <p style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.25rem;">{{ now()->format('F Y') }}</p>
    </div>

    <!-- Table -->
    <div class="table-container" style="border-radius: var(--radius-md); overflow: hidden;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Duration</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendance as $record)
                    <tr>
                        <td>{{ $record->date->format('M d, Y') }}</td>
                        <td>
                            <span style="padding: 0.25rem 0.75rem; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 600; @if($record->status === 'present') background: rgba(16, 185, 129, 0.1); color: #10b981; @elseif($record->status === 'absent') background: rgba(239, 68, 68, 0.1); color: #ef4444; @elseif($record->status === 'leave') background: rgba(59, 130, 246, 0.1); color: #3b82f6; @else background: rgba(245, 158, 11, 0.1); color: #f59e0b; @endif">
                                {{ ucfirst($record->status) }}
                            </span>
                        </td>
                        <td>{{ $record->check_in ? $record->check_in->format('H:i A') : '-' }}</td>
                        <td>{{ $record->check_out ? $record->check_out->format('H:i A') : '-' }}</td>
                        <td>{{ $record->duration_minutes ? $record->duration_minutes . ' min' : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No attendance records for this month</td>
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
