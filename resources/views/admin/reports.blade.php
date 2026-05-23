@extends('layouts.app')

@section('title', 'System Reports')

@section('content')
<div style="display: flex; flex-direction: column; gap: 2rem;" class="animate-fade-in">
    <!-- Header with export action -->
    <div class="glass-panel" style="padding: 1.5rem 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.5rem; margin-bottom: 0.25rem;"><i class="fa-solid fa-chart-column" style="color: var(--primary); margin-right: 0.5rem;"></i> Business Intelligence & Analytics</h2>
            <p style="color: var(--text-secondary); font-size: 0.875rem;">Monitor real-time occupancy rates, revenue, tax deductions, and download financial reports.</p>
        </div>
        <div>
            <a href="{{ route('admin.reports.export') }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-file-csv"></i> Export CSV Report
            </a>
        </div>
    </div>

    <!-- Analytics Key Metrics Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem;">
        <!-- Occupancy Gauge -->
        <div class="glass-panel" style="display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <span style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Occupancy Rate</span>
                <h3 style="font-size: 2.25rem; margin: 0.5rem 0; font-family: 'Outfit';">{{ $occupancyRate }}%</h3>
            </div>
            <div style="margin-top: 1rem;">
                <div style="width: 100%; height: 8px; background: var(--border-color); border-radius: var(--radius-full); overflow: hidden;">
                    <div style="width: {{ $occupancyRate }}%; height: 100%; background: linear-gradient(90deg, var(--primary), var(--secondary)); border-radius: var(--radius-full);"></div>
                </div>
                <span style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.5rem; display: block;">Active rooms booked right now.</span>
            </div>
        </div>

        <!-- Total Earnings -->
        <div class="glass-panel" style="display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <span style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Total Revenue</span>
                <h3 style="font-size: 2.25rem; margin: 0.5rem 0; font-family: 'Outfit'; color: var(--success);">${{ number_format($totalEarnings, 2) }}</h3>
            </div>
            <span style="font-size: 0.75rem; color: var(--text-secondary); display: block;">Accumulated bookings net of refunds.</span>
        </div>

        <!-- Refunded Amount -->
        <div class="glass-panel" style="display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <span style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Refunds Processed</span>
                <h3 style="font-size: 2.25rem; margin: 0.5rem 0; font-family: 'Outfit'; color: var(--danger);">${{ number_format($refundedAmount, 2) }}</h3>
            </div>
            <span style="font-size: 0.75rem; color: var(--text-secondary); display: block;">Total volume of returned guest payments.</span>
        </div>

        <!-- Tax Metrics -->
        @php
            $taxRate = floatval(\App\Models\Setting::getValue('tax_rate', '12'));
            $estimatedTax = $totalEarnings * ($taxRate / 100);
        @endphp
        <div class="glass-panel" style="display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <span style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Tax Summary ({{ $taxRate }}%)</span>
                <h3 style="font-size: 2.25rem; margin: 0.5rem 0; font-family: 'Outfit'; color: var(--info);">${{ number_format($estimatedTax, 2) }}</h3>
            </div>
            <span style="font-size: 0.75rem; color: var(--text-secondary); display: block;">Estimated VAT / tourist tax collection.</span>
        </div>
    </div>

    <!-- Monthly Revenue Visual Breakdown -->
    <div class="glass-panel" style="padding: 2rem;">
        <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem;"><i class="fa-solid fa-chart-line" style="color: var(--primary); margin-right: 0.5rem;"></i> Monthly Revenue Chart</h3>
        
        @php
            $maxEarning = max($earningsData) > 0 ? max($earningsData) : 100;
        @endphp

        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @foreach($months as $index => $monthName)
                @php
                    $earning = $earningsData[$index];
                    $pct = ($earning / $maxEarning) * 100;
                @endphp
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 100px; font-size: 0.875rem; font-weight: 600; color: var(--text-secondary); text-align: right;">
                        {{ $monthName }}
                    </div>
                    <div style="flex-grow: 1; height: 24px; background-color: var(--border-color); border-radius: var(--radius-sm); overflow: hidden; display: flex; align-items: center; position: relative;">
                        <div style="width: {{ $pct }}%; height: 100%; background: linear-gradient(90deg, var(--primary-light), var(--primary)); border-radius: var(--radius-sm); transition: width 1s ease-in-out;"></div>
                        @if($earning > 0)
                            <span style="position: absolute; left: 10px; font-size: 0.75rem; font-weight: 700; color: #fff; text-shadow: 0 1px 2px rgba(0,0,0,0.5);">
                                ${{ number_format($earning, 2) }}
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
