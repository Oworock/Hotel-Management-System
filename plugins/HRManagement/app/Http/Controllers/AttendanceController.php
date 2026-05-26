<?php

namespace Plugins\HRManagement\app\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Plugins\HRManagement\app\Models\{Attendance, Employee};
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->filled('date') ? $request->date : now()->format('Y-m-d');

        $attendance = Attendance::with('employee')
            ->whereDate('date', $date)
            ->paginate(20);

        return view('hrmanagement::attendance.index', compact('attendance', 'date'));
    }

    public function mark()
    {
        $employees = Employee::where('status', 'active')->get();
        $today = now()->format('Y-m-d');
        $todayAttendance = Attendance::whereDate('date', $today)->pluck('employee_id')->toArray();

        return view('hrmanagement::attendance.mark', compact('employees', 'todayAttendance'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,leave,half_day',
        ]);

        $attendance = Attendance::firstOrCreate(
            ['employee_id' => $validated['employee_id'], 'date' => $validated['date']],
            $validated
        );

        $attendance->update($validated);
        return redirect()->route('admin.hr.attendance.mark')->with('success', 'Attendance marked');
    }

    public function report(Request $request)
    {
        $query = Attendance::with('employee');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        $attendance = $query->orderBy('date', 'desc')->paginate(20);
        $employees = Employee::where('status', 'active')->get();

        return view('hrmanagement::attendance.report', compact('attendance', 'employees'));
    }
}
