@extends('layouts.app')

@section('title', 'My Payslips')

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 1.5rem 2rem 2.5rem 2rem;">
    <!-- Header -->
    <div style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;">
            <i class="fa-solid fa-receipt" style="color: var(--primary); margin-right: 0.5rem;"></i> My Payslips
        </h2>
    </div>

    <!-- Table -->
    <div class="table-container" style="border-radius: var(--radius-md); overflow: hidden;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Period</th>
                    <th>Salary</th>
                    <th>Deductions</th>
                    <th>Net Amount</th>
                    <th>Status</th>
                    <th style="text-align: center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payslips as $slip)
                    <tr>
                        <td>
                            <strong>{{ \Carbon\Carbon::createFromFormat('m', $slip->month)->format('F') }} {{ $slip->year }}</strong>
                        </td>
                        <td>${{ number_format($slip->basic_salary + $slip->allowances, 2) }}</td>
                        <td>${{ number_format($slip->deductions + $slip->tax, 2) }}</td>
                        <td>
                            <strong style="color: var(--success);">${{ number_format($slip->net_salary, 2) }}</strong>
                        </td>
                        <td>
                            <span style="padding: 0.25rem 0.75rem; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 600; @if($slip->status === 'paid') background: rgba(16, 185, 129, 0.1); color: #10b981; @else background: rgba(59, 130, 246, 0.1); color: #3b82f6; @endif">
                                {{ ucfirst($slip->status) }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route('admin.hr.payroll.slip', $slip) }}" target="_blank" class="btn" style="display: inline-flex; align-items: center; gap: 0.5rem; background: var(--primary); color: #fff; border: none; text-decoration: none; padding: 0.5rem 1rem; border-radius: var(--radius-sm); font-weight: 600; font-size: 0.85rem; cursor: pointer;">
                                <i class="fa-solid fa-download"></i> Download
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No payslips available yet</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="margin-top: 1.5rem;">
        {{ $payslips->links() }}
    </div>
</div>
@endsection
