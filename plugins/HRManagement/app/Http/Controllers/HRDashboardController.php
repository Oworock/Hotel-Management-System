<?php

namespace Plugins\HRManagement\app\Http\Controllers;

use Illuminate\Routing\Controller;
use Plugins\HRManagement\app\Models\{Employee, Department, Attendance, Leave, Payroll, PerformanceReview};
use Carbon\Carbon;

class HRDashboardController extends Controller
{
    public function dashboard()
    {
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('status', 'active')->count();
        $newThisMonth = Employee::whereMonth('hired_date', now()->month)->count();

        $absentToday = Attendance::whereDate('date', now())
            ->where('status', 'absent')
            ->count();

        $onLeaveToday = Attendance::whereDate('date', now())
            ->where('status', 'leave')
            ->count();

        $pendingLeaves = Leave::where('status', 'pending')->count();
        $totalDepartments = Department::where('is_active', true)->count();

        $payrollStatus = Payroll::groupBy('status')
            ->selectRaw('status, count(*) as count')
            ->get()
            ->pluck('count', 'status');

        $recentEmployees = Employee::latest()->limit(5)->get();

        return view('hrmanagement::dashboard', [
            'totalEmployees' => $totalEmployees,
            'activeEmployees' => $activeEmployees,
            'newThisMonth' => $newThisMonth,
            'absentToday' => $absentToday,
            'onLeaveToday' => $onLeaveToday,
            'pendingLeaves' => $pendingLeaves,
            'totalDepartments' => $totalDepartments,
            'payrollStatus' => $payrollStatus,
            'recentEmployees' => $recentEmployees
        ]);
    }
}
