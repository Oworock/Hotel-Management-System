@extends('layouts.app')

@section('title', 'My HR Profile')

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 1.5rem 2rem 2.5rem 2rem;">
    <!-- Header -->
    <div style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;">
            <i class="fa-solid fa-user" style="color: var(--primary); margin-right: 0.5rem;"></i> My HR Profile
        </h2>
        <p style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.25rem;">View your employment details and performance</p>
    </div>

    @php
        $employee = \Plugins\HRManagement\app\Models\Employee::where('user_id', auth()->id())->first();
    @endphp

    @if($employee)
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
            <!-- Left Column -->
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                <!-- Employment Details -->
                <div class="glass-panel" style="padding: 1.5rem; border-radius: var(--radius-md);">
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1.5rem; margin-top: 0;">
                        <i class="fa-solid fa-briefcase" style="color: var(--primary); margin-right: 0.5rem;"></i> Employment Details
                    </h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                        <div>
                            <label style="display: block; font-size: 0.75rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 0.25rem;">Employee Code</label>
                            <p style="font-size: 1rem; color: var(--text-primary); margin: 0; font-weight: 600;">{{ $employee->employee_code }}</p>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 0.25rem;">Department</label>
                            <p style="font-size: 1rem; color: var(--text-primary); margin: 0; font-weight: 600;">{{ $employee->department->name }}</p>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 0.25rem;">Designation</label>
                            <p style="font-size: 1rem; color: var(--text-primary); margin: 0; font-weight: 600;">{{ $employee->designation->name }}</p>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 0.25rem;">Employment Type</label>
                            <p style="font-size: 1rem; color: var(--text-primary); margin: 0; font-weight: 600;">{{ ucfirst($employee->employment_type) }}</p>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 0.25rem;">Hired Date</label>
                            <p style="font-size: 1rem; color: var(--text-primary); margin: 0; font-weight: 600;">{{ $employee->hired_date->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 0.25rem;">Status</label>
                            <p style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 600; margin: 0; @if($employee->status === 'active') background: rgba(16, 185, 129, 0.1); color: #10b981; @elseif($employee->status === 'inactive') background: rgba(245, 158, 11, 0.1); color: #f59e0b; @else background: rgba(239, 68, 68, 0.1); color: #ef4444; @endif">
                                {{ ucfirst($employee->status) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Attendance Summary -->
                <div class="glass-panel" style="padding: 1.5rem; border-radius: var(--radius-md);">
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1.5rem; margin-top: 0;">
                        <i class="fa-solid fa-calendar-check" style="color: var(--primary); margin-right: 0.5rem;"></i> Attendance Summary (This Month)
                    </h3>
                    @php
                        $thisMonth = now()->month;
                        $thisYear = now()->year;
                        $attendance = $employee->attendance()
                            ->whereMonth('created_at', $thisMonth)
                            ->whereYear('created_at', $thisYear)
                            ->get();
                        $present = $attendance->where('status', 'present')->count();
                        $absent = $attendance->where('status', 'absent')->count();
                        $leave = $attendance->where('status', 'leave')->count();
                        $half = $attendance->where('status', 'half_day')->count();
                    @endphp
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
                        <div style="padding: 1.5rem; background: var(--bg-secondary); border-radius: var(--radius-sm); text-align: center; border: 1px solid var(--border-color);">
                            <div style="font-size: 1.75rem; font-weight: 700; color: var(--success);">{{ $present }}</div>
                            <div style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.5rem;">Present</div>
                        </div>
                        <div style="padding: 1.5rem; background: var(--bg-secondary); border-radius: var(--radius-sm); text-align: center; border: 1px solid var(--border-color);">
                            <div style="font-size: 1.75rem; font-weight: 700; color: var(--danger);">{{ $absent }}</div>
                            <div style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.5rem;">Absent</div>
                        </div>
                        <div style="padding: 1.5rem; background: var(--bg-secondary); border-radius: var(--radius-sm); text-align: center; border: 1px solid var(--border-color);">
                            <div style="font-size: 1.75rem; font-weight: 700; color: var(--info);">{{ $leave }}</div>
                            <div style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.5rem;">On Leave</div>
                        </div>
                        <div style="padding: 1.5rem; background: var(--bg-secondary); border-radius: var(--radius-sm); text-align: center; border: 1px solid var(--border-color);">
                            <div style="font-size: 1.75rem; font-weight: 700; color: var(--text-secondary);">{{ $half }}</div>
                            <div style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.5rem;">Half Day</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                <!-- Compensation Info -->
                <div class="glass-panel" style="padding: 1.5rem; border-radius: var(--radius-md);">
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1.5rem; margin-top: 0;">
                        <i class="fa-solid fa-dollar-sign" style="color: var(--primary); margin-right: 0.5rem;"></i> Compensation
                    </h3>
                    <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.75rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 0.25rem;">Basic Salary</label>
                            <p style="font-size: 1.25rem; color: var(--primary); margin: 0; font-weight: 700;">${{ number_format($employee->basic_salary, 2) }}</p>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 0.25rem;">Allowances</label>
                            <p style="font-size: 1rem; color: var(--text-primary); margin: 0; font-weight: 600;">${{ number_format($employee->allowances, 2) }}</p>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 0.25rem;">Deductions</label>
                            <p style="font-size: 1rem; color: var(--danger); margin: 0; font-weight: 600;">${{ number_format($employee->deductions, 2) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Performance Ratings -->
                <div class="glass-panel" style="padding: 1.5rem; border-radius: var(--radius-md);">
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1.5rem; margin-top: 0;">
                        <i class="fa-solid fa-star" style="color: var(--primary); margin-right: 0.5rem;"></i> Performance Ratings
                    </h3>
                    @php
                        $recentReviews = $employee->reviews()->latest('review_date')->limit(3)->get();
                    @endphp
                    @forelse($recentReviews as $review)
                        <div style="padding: 1rem; background: var(--bg-secondary); border-radius: var(--radius-sm); border-left: 3px solid var(--primary); margin-bottom: 1rem;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                                <strong style="color: var(--text-primary);">{{ $review->reviewedBy->full_name }}</strong>
                                <div style="font-size: 0.85rem;">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fa-solid fa-star" style="color: @if($i <= $review->rating) var(--warning) @else var(--text-secondary) @endif; opacity: @if($i <= $review->rating) 1 @else 0.3 @endif;"></i>
                                    @endfor
                                </div>
                            </div>
                            <small style="color: var(--text-secondary); font-size: 0.75rem;">{{ $review->review_date->format('M d, Y') }}</small>
                            @if($review->comments)
                                <p style="margin-top: 0.5rem; margin-bottom: 0; font-size: 0.85rem; color: var(--text-primary);">{{ $review->comments }}</p>
                            @endif
                        </div>
                    @empty
                        <p style="color: var(--text-secondary); text-align: center; margin: 1rem 0;">No performance reviews yet</p>
                    @endforelse
                </div>

                <!-- Leave Balance -->
                <div class="glass-panel" style="padding: 1.5rem; border-radius: var(--radius-md);">
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1.5rem; margin-top: 0;">
                        <i class="fa-solid fa-umbrella" style="color: var(--primary); margin-right: 0.5rem;"></i> Leave Balance
                    </h3>
                    @php
                        $leaveTypes = \Plugins\HRManagement\app\Models\LeaveType::where('is_active', true)->get();
                        $thisYear = now()->year;
                    @endphp
                    @forelse($leaveTypes as $type)
                        @php
                            $used = $employee->leaves()
                                ->where('leave_type_id', $type->id)
                                ->where('status', 'approved')
                                ->whereYear('created_at', $thisYear)
                                ->sum('days');
                            $remaining = $type->days_per_year - $used;
                        @endphp
                        <div style="margin-bottom: 1.5rem;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; align-items: center;">
                                <strong style="color: var(--text-primary);">{{ $type->name }}</strong>
                                <span style="font-size: 0.875rem; color: var(--text-secondary);">{{ $remaining }}/{{ $type->days_per_year }}</span>
                            </div>
                            <div style="height: 0.5rem; background: var(--bg-secondary); border-radius: var(--radius-sm); overflow: hidden;">
                                <div style="height: 100%; width: {{ ($remaining / $type->days_per_year) * 100 }}%; background: @if($remaining > $type->days_per_year / 2) var(--success) @elseif($remaining > $type->days_per_year / 4) var(--warning) @else var(--danger) @endif; transition: width 0.3s ease;"></div>
                            </div>
                        </div>
                    @empty
                        <p style="color: var(--text-secondary); text-align: center;">No leave types available</p>
                    @endforelse
                </div>
            </div>
        </div>
    @else
        <div style="padding: 1.5rem; background: rgba(59, 130, 246, 0.1); color: #3b82f6; border-radius: var(--radius-md); border-left: 4px solid #3b82f6;">
            <i class="fa-solid fa-info-circle" style="margin-right: 0.5rem;"></i> Your HR profile is not yet set up. Please contact HR for assistance.
        </div>
    @endif
</div>
@endsection
