@extends('layouts.app')

@section('title', 'Manage Payments')

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 1.5rem 2rem;">
    <!-- Header & Search -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;">
        <div>
            <h2 style="font-size: 1.5rem; margin-bottom: 0.25rem;"><i class="fa-solid fa-credit-card" style="color: var(--primary); margin-right: 0.5rem;"></i> Payments Ledger</h2>
            <p style="color: var(--text-secondary); font-size: 0.875rem;">Audit transaction references, payment methods, processing logs, and process refunds.</p>
        </div>
        
        <form action="{{ route('admin.payments') }}" method="GET" style="display: flex; gap: 0.5rem; width: 100%; max-width: 400px; margin: 0;">
            <div class="form-group" style="margin: 0; flex-grow: 1; display: flex; gap: 0.5rem;">
                <input type="text" name="search" class="form-control" placeholder="Search Transaction ID or Guest..." value="{{ request('search') }}" style="width: 100%;">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                @if(request()->filled('search'))
                    <a href="{{ route('admin.payments') }}" class="btn btn-outline" style="display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-rotate-left"></i></a>
                @endif
            </div>
        </form>
    </div>

    <!-- Payments Table -->
    <div class="table-container" style="border: none; box-shadow: var(--shadow-sm); border-radius: var(--radius-md); overflow: hidden;">
        <table class="table">
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Transaction Reference</th>
                    <th>Booking / Guest</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td style="font-weight: 700;">#{{ $payment->id }}</td>
                        <td>
                            <span style="font-family: monospace; font-weight: 600; color: var(--primary);">{{ $payment->transaction_id }}</span>
                        </td>
                        <td>
                            @if($payment->booking)
                                <div style="font-weight: 600;">
                                    <a href="{{ route('admin.bookings', ['search' => $payment->booking->customer->email ?? '']) }}" style="color: inherit; text-decoration: underline;">
                                        Booking #{{ $payment->booking->id }}
                                    </a>
                                </div>
                                <div style="font-size: 0.8rem; color: var(--text-secondary);">
                                    Guest: {{ $payment->booking->customer->name ?? 'N/A' }}
                                </div>
                            @else
                                <span style="color: var(--danger); font-style: italic;">No Booking Linked</span>
                            @endif
                        </td>
                        <td style="font-weight: 700; font-size: 1rem; color: var(--text-primary);">
                            ${{ number_format($payment->amount, 2) }}
                        </td>
                        <td>
                            <span class="badge" style="background-color: var(--primary-glow); color: var(--primary); border: 1px solid var(--primary-light); text-transform: uppercase; font-size: 0.75rem;">
                                {{ str_replace('_', ' ', $payment->payment_method) }}
                            </span>
                        </td>
                        <td style="font-size: 0.85rem; color: var(--text-secondary);">
                            {{ $payment->created_at->format('Y-m-d H:i') }}
                        </td>
                        <td>
                            <span class="badge badge-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'refunded' ? 'danger' : 'warning') }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            @if($payment->status === 'completed')
                                <form action="{{ route('admin.payments.refund', $payment->id) }}" method="POST" style="margin: 0; display: inline-block;" onsubmit="return confirm('Are you sure you want to issue a refund for this payment? This will also cancel the linked booking.')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fa-solid fa-arrow-rotate-left"></i> Refund
                                    </button>
                                </form>
                            @else
                                <span style="font-size: 0.85rem; color: var(--text-muted); font-style: italic;">No actions</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--text-secondary); padding: 4rem 0;">
                            <i class="fa-solid fa-receipt" style="font-size: 3rem; margin-bottom: 1rem; color: var(--border-color); display: block;"></i>
                            <p>No payments recorded.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($payments->hasPages())
        <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
            {{ $payments->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
