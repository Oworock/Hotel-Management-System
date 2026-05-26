<?php

namespace Plugins\HRManagement\app\Http\Controllers;

use Illuminate\Routing\Controller;
use Plugins\HRManagement\app\Models\{Employee, Attendance, Leave, Payroll};
use Carbon\Carbon;

class StaffHRController extends Controller
{
    public function profile()
    {
        $employee = Employee::where('user_id', auth()->id())->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'HR profile not found. Please contact HR.');
        }

        return view('hrmanagement::staff-profile', compact('employee'));
    }

    public function leaves()
    {
        $employee = Employee::where('user_id', auth()->id())->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'HR profile not found.');
        }

        $leaves = $employee->leaves()->with('leaveType', 'approvedBy')->latest()->paginate(10);

        return view('hrmanagement::staff-leaves', compact('employee', 'leaves'));
    }

    public function attendance()
    {
        $employee = Employee::where('user_id', auth()->id())->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'HR profile not found.');
        }

        $thisMonth = now()->month;
        $thisYear = now()->year;

        $attendance = $employee->attendance()
            ->whereMonth('created_at', $thisMonth)
            ->whereYear('created_at', $thisYear)
            ->latest()
            ->paginate(10);

        return view('hrmanagement::staff-attendance', compact('employee', 'attendance'));
    }

    public function payslips()
    {
        $employee = Employee::where('user_id', auth()->id())->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'HR profile not found.');
        }

        $payslips = $employee->payroll()->where('status', '!=', 'draft')->latest()->paginate(10);

        return view('hrmanagement::staff-payslips', compact('employee', 'payslips'));
    }
}
