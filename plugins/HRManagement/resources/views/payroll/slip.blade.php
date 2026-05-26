@extends('layouts.app')

@section('title', 'Payroll Slip')

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 1.5rem 2rem 2.5rem 2rem; max-width: 900px; margin: 0 auto;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;">
                <i class="fa-solid fa-receipt" style="color: var(--primary); margin-right: 0.5rem;"></i> Payroll Slip
            </h2>
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.25rem;">{{ $payroll->employee->full_name }}</p>
        </div>
        <button onclick="window.print()" class="btn" style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: #fff; border: none; padding: 0.75rem 1.5rem; border-radius: var(--radius-md); font-weight: 600; cursor: pointer;">
            <i class="fa-solid fa-print"></i> Print
        </button>
    </div>

    <!-- Main Content -->
    <div style="border-bottom: 2px solid var(--border-color); padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
        <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin: 0;">PAYROLL SLIP</h3>
        <p style="color: var(--text-secondary); margin-top: 0.5rem;">{{ \Carbon\Carbon::createFromFormat('m', $payroll->month)->format('F') }} {{ $payroll->year }}</p>
    </div>

    <!-- Employee & Payroll Details -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
        <div>
            <h4 style="font-weight: 600; color: var(--text-primary); margin-bottom: 1rem;">Employee Details</h4>
            <div style="font-size: 0.875rem; line-height: 1.8; color: var(--text-primary);">
                <div><strong>Name:</strong> {{ $payroll->employee->full_name }}</div>
                <div><strong>Code:</strong> {{ $payroll->employee->employee_code }}</div>
                <div><strong>Department:</strong> {{ $payroll->employee->department->name }}</div>
                <div><strong>Designation:</strong> {{ $payroll->employee->designation->name }}</div>
            </div>
        </div>

        <div>
            <h4 style="font-weight: 600; color: var(--text-primary); margin-bottom: 1rem;">Payroll Details</h4>
            <div style="font-size: 0.875rem; line-height: 1.8; color: var(--text-primary);">
                <div><strong>Period:</strong> {{ \Carbon\Carbon::createFromFormat('m', $payroll->month)->format('F') }} {{ $payroll->year }}</div>
                <div><strong>Status:</strong>
                    <span style="padding: 0.25rem 0.75rem; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 600; display: inline-block; margin-top: 0.25rem; @if($payroll->status === 'paid') background: rgba(16, 185, 129, 0.1); color: #10b981; @elseif($payroll->status === 'finalized') background: rgba(59, 130, 246, 0.1); color: #3b82f6; @else background: rgba(245, 158, 11, 0.1); color: #f59e0b; @endif">
                        {{ ucfirst($payroll->status) }}
                    </span>
                </div>
                @if($payroll->payment_date)
                    <div><strong>Payment Date:</strong> {{ $payroll->payment_date->format('M d, Y') }}</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Salary Breakdown Table -->
    <div style="border-top: 2px solid var(--border-color); border-bottom: 2px solid var(--border-color); padding: 1rem 0; margin-bottom: 2rem;">
        <table style="width: 100%;">
            <tbody>
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 0.75rem 0; color: var(--text-primary);"><strong>Basic Salary</strong></td>
                    <td style="padding: 0.75rem 0; text-align: right; color: var(--text-primary);">${{ number_format($payroll->basic_salary, 2) }}</td>
                </tr>
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 0.75rem 0; color: var(--text-primary);"><strong>Allowances</strong></td>
                    <td style="padding: 0.75rem 0; text-align: right; color: var(--text-primary);">${{ number_format($payroll->allowances, 2) }}</td>
                </tr>
                <tr style="border-bottom: 1px solid var(--border-color); background: var(--bg-secondary);">
                    <td style="padding: 0.75rem 0; color: var(--text-primary);"><strong>Gross Salary</strong></td>
                    <td style="padding: 0.75rem 0; text-align: right; color: var(--text-primary); font-weight: 700;">${{ number_format($payroll->basic_salary + $payroll->allowances, 2) }}</td>
                </tr>
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 0.75rem 0; color: var(--danger);"><strong>Deductions</strong></td>
                    <td style="padding: 0.75rem 0; text-align: right; color: var(--danger);">-${{ number_format($payroll->deductions, 2) }}</td>
                </tr>
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 0.75rem 0; color: var(--danger);"><strong>Tax</strong></td>
                    <td style="padding: 0.75rem 0; text-align: right; color: var(--danger);">-${{ number_format($payroll->tax, 2) }}</td>
                </tr>
                <tr style="background: rgba(16, 185, 129, 0.1);">
                    <td style="padding: 1rem 0; color: var(--success); font-weight: 700;"><strong>Net Salary</strong></td>
                    <td style="padding: 1rem 0; text-align: right; color: var(--success); font-weight: 700; font-size: 1.1rem;">${{ number_format($payroll->net_salary, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div style="text-align: center; font-size: 0.75rem; color: var(--text-secondary); padding: 1rem 0;">
        <p>This is a computer generated payroll slip and does not require a signature.</p>
        <p>Generated on {{ now()->format('M d, Y H:i') }}</p>
    </div>
</div>

<style>
    @media print {
        body {
            background: white;
        }
        .glass-panel {
            box-shadow: none;
        }
    }
</style>
@endsection
