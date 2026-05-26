<?php

namespace Plugins\HRManagement\app\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Plugins\HRManagement\app\Models\{Leave, LeaveType, Employee};

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $query = Leave::with('employee', 'leaveType');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $leaves = $query->latest()->paginate(15);
        $employees = Employee::where('status', 'active')->get();

        return view('hrmanagement::leaves.index', compact('leaves', 'employees'));
    }

    public function request()
    {
        $leaveTypes = LeaveType::where('is_active', true)->get();
        return view('hrmanagement::leaves.request', compact('leaveTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'reason' => 'nullable',
        ]);

        Leave::create($validated);
        return redirect()->route('admin.hr.leaves.index')->with('success', 'Leave request submitted');
    }

    public function approve(Request $request, Leave $leave)
    {
        $leave->update([
            'status' => 'approved',
            'approved_by_id' => auth()->id()
        ]);
        return redirect()->back()->with('success', 'Leave approved');
    }

    public function reject(Request $request, Leave $leave)
    {
        $leave->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Leave rejected');
    }
}
