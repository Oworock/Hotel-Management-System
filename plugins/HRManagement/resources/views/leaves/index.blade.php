@extends('layouts.app')

@section('title', 'Leave Requests')

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 1.5rem 2rem 2.5rem 2rem;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;">
                <i class="fa-solid fa-umbrella" style="color: var(--primary); margin-right: 0.5rem;"></i> Leave Requests
            </h2>
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.25rem;">Manage leave requests and approvals</p>
        </div>
        <a href="{{ route('admin.hr.leaves.request') }}" class="btn" style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, var(--warning), var(--primary-light)); color: #fff; border: none; text-decoration: none; padding: 0.75rem 1.5rem; border-radius: var(--radius-md); font-weight: 600; cursor: pointer;">
            <i class="fa-solid fa-plus"></i> Request Leave
        </a>
    </div>

    <!-- Filter -->
    <div class="glass-panel" style="padding: 1.5rem; margin-bottom: 1.5rem; border-radius: var(--radius-md);">
        <form method="GET" style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 1rem;">
            <select name="status" style="padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">
                <option value="">All Status</option>
                <option value="pending" @selected(request('status') == 'pending')>Pending</option>
                <option value="approved" @selected(request('status') == 'approved')>Approved</option>
                <option value="rejected" @selected(request('status') == 'rejected')>Rejected</option>
            </select>
            <select name="employee_id" style="padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">
                <option value="">All Employees</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>{{ $emp->full_name }}</option>
                @endforeach
            </select>
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
                    <th>Leave Type</th>
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>Days</th>
                    <th>Status</th>
                    <th style="text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leaves as $leave)
                    <tr>
                        <td><strong>{{ $leave->employee->full_name }}</strong></td>
                        <td>{{ $leave->leaveType->name }}</td>
                        <td>{{ $leave->from_date->format('M d, Y') }}</td>
                        <td>{{ $leave->to_date->format('M d, Y') }}</td>
                        <td><strong>{{ $leave->days }}</strong></td>
                        <td>
                            <span style="padding: 0.25rem 0.75rem; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 600; @if($leave->status === 'pending') background: rgba(245, 158, 11, 0.1); color: #f59e0b; @elseif($leave->status === 'approved') background: rgba(16, 185, 129, 0.1); color: #10b981; @else background: rgba(239, 68, 68, 0.1); color: #ef4444; @endif">
                                {{ ucfirst($leave->status) }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            @if($leave->status === 'pending')
                                <div style="display: flex; gap: 0.5rem; justify-content: center; font-size: 0.85rem;">
                                    <form action="{{ route('admin.hr.leaves.approve', $leave) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" style="background: none; border: none; color: var(--success); text-decoration: none; font-weight: 600; cursor: pointer;">Approve</button>
                                    </form>
                                    <form action="{{ route('admin.hr.leaves.reject', $leave) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" style="background: none; border: none; color: var(--danger); text-decoration: none; font-weight: 600; cursor: pointer;">Reject</button>
                                    </form>
                                </div>
                            @else
                                <span style="color: var(--text-secondary); font-size: 0.85rem;">No action</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No leave requests found</td>
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
