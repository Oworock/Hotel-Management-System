@extends('layouts.app')

@section('title', 'Staff Shift Tracking')

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 0;">
    <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
        <h2 style="font-size: 1.25rem;"><i class="fa-solid fa-clock-rotate-left" style="color: var(--primary);"></i> Employee Shift History</h2>
    </div>
    
    <div class="table-container" style="border: none;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Email</th>
                    <th>Clocked In</th>
                    <th>Clocked Out</th>
                    <th>Duration (Hrs:Mins)</th>
                    <th>Shift Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($shifts as $shift)
                    <tr>
                        <td style="font-weight: 600;">{{ $shift->user->name }}</td>
                        <td>{{ $shift->user->email }}</td>
                        <td style="font-size: 0.9rem;">
                            {{ $shift->clock_in_at->format('M d, Y - h:i A') }}
                        </td>
                        <td style="font-size: 0.9rem;">
                            @if($shift->clock_out_at)
                                {{ $shift->clock_out_at->format('M d, Y - h:i A') }}
                            @else
                                <span style="color: var(--text-muted); font-style: italic;">N/A</span>
                            @endif
                        </td>
                        <td style="font-family: monospace; font-weight: 600;">
                            @if($shift->clock_out_at)
                                @php
                                    $hours = floor($shift->duration_minutes / 60);
                                    $mins = $shift->duration_minutes % 60;
                                @endphp
                                {{ sprintf('%02d:%02d', $hours, $mins) }}
                            @else
                                <span style="color: var(--primary); font-weight: 700;">Ongoing</span>
                            @endif
                        </td>
                        <td>
                            @if($shift->clock_out_at)
                                <span class="badge badge-success" style="opacity: 0.75;">Completed</span>
                            @else
                                <span class="badge badge-primary" style="animation: pulseGlow 2s infinite;">Active Now</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 4rem 0;">
                            <i class="fa-regular fa-clock" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i> No shifts logged yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($shifts->hasPages())
        <div style="padding: 1.5rem; display: flex; justify-content: center; border-top: 1px solid var(--border-color);">
            {{ $shifts->links() }}
        </div>
    @endif
</div>
@endsection
