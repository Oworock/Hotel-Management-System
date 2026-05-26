@extends('layouts.app')

@section('title', 'My Leave Requests')

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 1.5rem 2rem 2.5rem 2rem;">
    <!-- Header -->
    <div style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;">
            <i class="fa-solid fa-umbrella" style="color: var(--primary); margin-right: 0.5rem;"></i> My Leave Requests
        </h2>
    </div>

    <!-- Table -->
    <div class="table-container" style="border-radius: var(--radius-md); overflow: hidden;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Leave Type</th>
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>Days</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leaves as $leave)
                    <tr>
                        <td>{{ $leave->leaveType->name }}</td>
                        <td>{{ $leave->from_date->format('M d, Y') }}</td>
                        <td>{{ $leave->to_date->format('M d, Y') }}</td>
                        <td><strong>{{ $leave->days }}</strong></td>
                        <td>
                            <span style="padding: 0.25rem 0.75rem; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 600; @if($leave->status === 'pending') background: rgba(245, 158, 11, 0.1); color: #f59e0b; @elseif($leave->status === 'approved') background: rgba(16, 185, 129, 0.1); color: #10b981; @else background: rgba(239, 68, 68, 0.1); color: #ef4444; @endif">
                                {{ ucfirst($leave->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No leave requests found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="margin-top: 1.5rem;">
        {{ $leaves->links() }}
    </div>
</div>
@endsection
