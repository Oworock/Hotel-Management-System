@extends('layouts.app')

@section('title', 'Payroll Management')

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 1.5rem 2rem 2.5rem 2rem;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;">
                <i class="fa-solid fa-money-bill" style="color: var(--primary); margin-right: 0.5rem;"></i> Payroll Management
            </h2>
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.25rem;">Manage employee salaries and payments</p>
        </div>
        <a href="{{ route('admin.hr.payroll.create') }}" class="btn" style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, var(--info), var(--primary-light)); color: #fff; border: none; text-decoration: none; padding: 0.75rem 1.5rem; border-radius: var(--radius-md); font-weight: 600; cursor: pointer;">
            <i class="fa-solid fa-plus"></i> Create Payroll
        </a>
    </div>

    <!-- Filter -->
    <div class="glass-panel" style="padding: 1.5rem; margin-bottom: 1.5rem; border-radius: var(--radius-md);">
        <form method="GET" style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 1rem;">
            <input type="month" name="month" value="{{ request('month') }}" style="padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">
            <select name="status" style="padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">
                <option value="">All Status</option>
                <option value="draft" @selected(request('status') == 'draft')>Draft</option>
                <option value="finalized" @selected(request('status') == 'finalized')>Finalized</option>
                <option value="paid" @selected(request('status') == 'paid')>Paid</option>
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
                    <th>Month</th>
                    <th>Salary</th>
                    <th>Deductions</th>
                    <th>Net Amount</th>
                    <th>Status</th>
                    <th style="text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payroll as $entry)
                    <tr>
                        <td><strong>{{ $entry->employee->full_name }}</strong></td>
                        <td>{{ \Carbon\Carbon::createFromFormat('m', $entry->month)->format('F') }} {{ $entry->year }}</td>
                        <td>${{ number_format($entry->basic_salary, 2) }}</td>
                        <td>${{ number_format($entry->deductions, 2) }}</td>
                        <td style="color: var(--success); font-weight: 700;">${{ number_format($entry->net_salary, 2) }}</td>
                        <td>
                            <span style="padding: 0.25rem 0.75rem; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 600; @if($entry->status === 'paid') background: rgba(16, 185, 129, 0.1); color: #10b981; @elseif($entry->status === 'finalized') background: rgba(59, 130, 246, 0.1); color: #3b82f6; @else background: rgba(245, 158, 11, 0.1); color: #f59e0b; @endif">
                                {{ ucfirst($entry->status) }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 0.5rem; justify-content: center; font-size: 0.85rem;">
                                <a href="{{ route('admin.hr.payroll.slip', $entry) }}" style="color: var(--primary); text-decoration: none; font-weight: 600;">Slip</a>
                                @if($entry->status === 'draft')
                                    <form action="{{ route('admin.hr.payroll.finalize', $entry) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" style="background: none; border: none; color: var(--success); text-decoration: none; font-weight: 600; cursor: pointer;">Finalize</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No payroll records found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="margin-top: 1.5rem;">
        {{ $payroll->links() }}
    </div>
</div>
@endsection
