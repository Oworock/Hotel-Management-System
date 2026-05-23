@extends('layouts.app')

@section('title', 'E-commerce Sales Analytics')

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 1.5rem 2rem 2.5rem 2rem;">
    <!-- Title and Header Actions -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;">
                <i class="fa-solid fa-chart-simple" style="color: var(--primary); margin-right: 0.5rem;"></i> Sales & Revenue Analytics
            </h2>
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.25rem;">Performance analytics and revenue distribution graphs.</p>
        </div>
        <div>
            <a href="{{ route('admin.ecommerce.dashboard') }}" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Overview Grid -->
    <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 2rem; margin-bottom: 2.5rem; align-items: stretch;">
        <!-- Revenue Distribution: Tuck Shop vs Restaurant -->
        <div class="glass-panel" style="padding: 1.5rem; border-radius: var(--radius-md); display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <h3 style="font-size: 1.2rem; font-weight: 600; margin-top: 0; margin-bottom: 1.5rem; color: var(--text-primary);">
                    <i class="fa-solid fa-pizza-slice" style="color: var(--primary); margin-right: 0.5rem;"></i> Store vs Restaurant Revenue
                </h3>
                
                @php
                    $tuckShopRev = $typeRevenue['tuck_shop'] ?? 0;
                    $restaurantRev = $typeRevenue['restaurant'] ?? 0;
                    $totalRev = $tuckShopRev + $restaurantRev;
                    
                    $tuckShopPct = $totalRev > 0 ? ($tuckShopRev / $totalRev) * 100 : 0;
                    $restaurantPct = $totalRev > 0 ? ($restaurantRev / $totalRev) * 100 : 0;
                @endphp

                <!-- Custom Visual Chart -->
                <div style="margin-bottom: 2rem;">
                    <div style="display: flex; height: 32px; border-radius: 16px; overflow: hidden; background: var(--border-color); box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);">
                        @if($totalRev > 0)
                            <div style="width: {{ $tuckShopPct }}%; background: linear-gradient(135deg, var(--primary), var(--primary-light)); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.8rem; font-weight: 700; transition: width 0.5s ease;" title="Tuck Shop: {{ number_format($tuckShopPct, 1) }}%">
                                @if($tuckShopPct > 15) {{ number_format($tuckShopPct, 0) }}% @endif
                            </div>
                            <div style="width: {{ $restaurantPct }}%; background: linear-gradient(135deg, var(--secondary), var(--secondary-light)); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.8rem; font-weight: 700; transition: width 0.5s ease;" title="Restaurant: {{ number_format($restaurantPct, 1) }}%">
                                @if($restaurantPct > 15) {{ number_format($restaurantPct, 0) }}% @endif
                            </div>
                        @else
                            <div style="width: 100%; display: flex; align-items: center; justify-content: center; color: var(--text-secondary); font-size: 0.85rem;">
                                No revenue recorded yet.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Breakdown Cards -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div style="padding: 1rem; border-radius: var(--radius-sm); background: var(--primary-glow); border-left: 4px solid var(--primary);">
                        <span style="font-size: 0.8rem; color: var(--text-secondary); font-weight: 600; text-transform: uppercase;">Tuck Shop</span>
                        <div style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin-top: 0.25rem;">
                            {{ $currency === 'USD' ? '$' : $currency }}{{ number_format($tuckShopRev, 2) }}
                        </div>
                        <span style="font-size: 0.75rem; color: var(--text-secondary);">{{ number_format($tuckShopPct, 1) }}% of total</span>
                    </div>

                    <div style="padding: 1rem; border-radius: var(--radius-sm); background: var(--secondary-glow); border-left: 4px solid var(--secondary);">
                        <span style="font-size: 0.8rem; color: var(--text-secondary); font-weight: 600; text-transform: uppercase;">Restaurant</span>
                        <div style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin-top: 0.25rem;">
                            {{ $currency === 'USD' ? '$' : $currency }}{{ number_format($restaurantRev, 2) }}
                        </div>
                        <span style="font-size: 0.75rem; color: var(--text-secondary);">{{ number_format($restaurantPct, 1) }}% of total</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales by Category (Bar list) -->
        <div class="glass-panel" style="padding: 1.5rem; border-radius: var(--radius-md); display: flex; flex-direction: column;">
            <h3 style="font-size: 1.2rem; font-weight: 600; margin-top: 0; margin-bottom: 1.25rem; color: var(--text-primary);">
                <i class="fa-solid fa-list-check" style="color: var(--primary); margin-right: 0.5rem;"></i> Category Sales Breakdown
            </h3>
            
            <div style="display: flex; flex-direction: column; gap: 1rem; flex: 1; justify-content: center;">
                @php
                    $maxCatSale = count($categorySales) > 0 ? max($categorySales) : 0;
                @endphp
                
                @forelse($categorySales as $category => $salesTotal)
                    @php
                        $catPct = $maxCatSale > 0 ? ($salesTotal / $maxCatSale) * 100 : 0;
                    @endphp
                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.4rem;">
                            <span style="color: var(--text-primary);">{{ ucfirst($category ?: 'uncategorized') }}</span>
                            <span style="color: var(--success);">{{ $currency === 'USD' ? '$' : $currency }}{{ number_format($salesTotal, 2) }}</span>
                        </div>
                        <div style="width: 100%; height: 8px; background: var(--border-color); border-radius: 4px; overflow: hidden;">
                            <div style="width: {{ $catPct }}%; height: 100%; background: linear-gradient(90deg, var(--primary), var(--primary-light)); border-radius: 4px;"></div>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; color: var(--text-secondary); padding: 1.5rem 0;">
                        No sales recorded for categories yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Daily Revenue Trend: 30-Day Bar Graph -->
    <div class="glass-panel" style="padding: 2rem; border-radius: var(--radius-md); margin-bottom: 2.5rem;">
        <h3 style="font-size: 1.25rem; font-weight: 600; margin-top: 0; margin-bottom: 1.5rem; color: var(--text-primary);">
            <i class="fa-solid fa-chart-line" style="color: var(--primary); margin-right: 0.5rem;"></i> Daily Revenue Trend (Last 30 Days)
        </h3>

        @php
            $maxDaily = 0;
            foreach ($dailyRaw as $day) {
                if ($day->total > $maxDaily) {
                    $maxDaily = $day->total;
                }
            }
            if ($maxDaily == 0) {
                $maxDaily = 100; // baseline
            }
        @endphp

        @if(count($dailyRaw) > 0)
            <!-- Custom CSS Chart Grid -->
            <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 1rem;">
                <div style="display: flex; align-items: flex-end; justify-content: space-between; height: 180px; padding: 0 1rem; border-bottom: 2px solid var(--border-color); position: relative; gap: 4px; overflow-x: auto;">
                    
                    @foreach($dailyRaw as $day)
                        @php
                            $heightPct = ($day->total / $maxDaily) * 100;
                            // Date format to simple representation
                            $dateObj = \Carbon\Carbon::parse($day->date);
                            $formattedDate = $dateObj->format('M d');
                        @endphp
                        <div style="flex: 1; display: flex; flex-direction: column; align-items: center; min-width: 15px; group" class="chart-bar-container">
                            <!-- Tooltip showing total sales -->
                            <div class="chart-tooltip" style="visibility: hidden; background: var(--text-primary); color: #fff; text-align: center; padding: 0.25rem 0.5rem; border-radius: 4px; position: absolute; bottom: calc({{ $heightPct }}% + 15px); font-size: 0.75rem; font-weight: 600; white-space: nowrap; box-shadow: var(--shadow-sm); z-index: 10;">
                                {{ $currency === 'USD' ? '$' : $currency }}{{ number_format($day->total, 2) }}
                            </div>
                            <!-- Graph bar -->
                            <div style="width: 100%; height: max(4px, {{ $heightPct }}%); background: linear-gradient(to top, var(--primary), var(--primary-light)); border-top-left-radius: 3px; border-top-right-radius: 3px; cursor: pointer; transition: all 0.2s;" 
                                 onmouseover="this.parentElement.querySelector('.chart-tooltip').style.visibility='visible'; this.style.opacity='0.85';" 
                                 onmouseout="this.parentElement.querySelector('.chart-tooltip').style.visibility='hidden'; this.style.opacity='1';">
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Label Row -->
                <div style="display: flex; justify-content: space-between; padding: 0 1rem; font-size: 0.75rem; color: var(--text-secondary); font-weight: 500;">
                    <span>{{ \Carbon\Carbon::parse($dailyRaw->first()->date)->format('M d') }}</span>
                    <span>Daily Sales Timeline</span>
                    <span>{{ \Carbon\Carbon::parse($dailyRaw->last()->date)->format('M d') }}</span>
                </div>
            </div>
        @else
            <div style="text-align: center; color: var(--text-secondary); padding: 3rem 0; background: rgba(0,0,0,0.02); border-radius: var(--radius-sm);">
                <i class="fa-solid fa-chart-area" style="font-size: 2.5rem; display: block; margin-bottom: 0.75rem; opacity: 0.5;"></i>
                No historical sales recorded for trend calculations.
            </div>
        @endif
    </div>

    <!-- Statuses and Details -->
    <div class="glass-panel" style="padding: 1.5rem; border-radius: var(--radius-md);">
        <h3 style="font-size: 1.2rem; font-weight: 600; margin-top: 0; margin-bottom: 1.25rem; color: var(--text-primary);">
            <i class="fa-solid fa-arrows-spin" style="color: var(--primary); margin-right: 0.5rem;"></i> Order Status Summary
        </h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
            @php
                $statusColors = [
                    'pending' => ['var(--warning)', 'var(--warning-glow)'],
                    'preparing' => ['var(--info)', 'var(--info-glow)'],
                    'completed' => ['var(--success)', 'var(--success-glow)'],
                    'delivered' => ['var(--success)', 'var(--success-glow)'],
                    'cancelled' => ['var(--danger)', 'var(--danger-glow)']
                ];
            @endphp

            @foreach(['pending', 'preparing', 'completed', 'delivered', 'cancelled'] as $status)
                @php
                    $count = $statusCounts[$status] ?? 0;
                    $colorPair = $statusColors[$status] ?? ['var(--text-secondary)', 'var(--border-color)'];
                @endphp
                <div style="padding: 1rem; border-radius: var(--radius-sm); background: {{ $colorPair[1] }}; text-align: center; border: 1px solid {{ $colorPair[0] }};">
                    <span style="font-size: 0.8rem; font-weight: 700; color: {{ $colorPair[0] }}; text-transform: uppercase; display: block; margin-bottom: 0.5rem;">
                        {{ ucfirst($status) }}
                    </span>
                    <span style="font-size: 2rem; font-weight: 700; color: var(--text-primary);">
                        {{ $count }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    .chart-bar-container {
        position: relative;
    }
</style>
@endsection
