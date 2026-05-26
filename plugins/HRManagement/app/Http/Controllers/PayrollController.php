<?php

namespace Plugins\HRManagement\app\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Plugins\HRManagement\app\Models\{Payroll, Employee};
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $query = Payroll::with('employee');

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payroll = $query->latest()->paginate(15);
        $employees = Employee::where('status', 'active')->get();

        return view('hrmanagement::payroll.index', compact('payroll', 'employees'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->get();
        $month = now()->month;
        $year = now()->year;
        return view('hrmanagement::payroll.form', compact('employees', 'month', 'year'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer',
            'basic_salary' => 'required|numeric',
            'allowances' => 'required|numeric',
            'deductions' => 'required|numeric',
            'tax' => 'required|numeric',
        ]);

        $payroll = Payroll::create($validated);
        $payroll->net_salary = $payroll->calculateNetSalary();
        $payroll->save();

        return redirect()->route('admin.hr.payroll.index')->with('success', 'Payroll entry created');
    }

    public function finalize(Payroll $payroll)
    {
        $payroll->update(['status' => 'finalized']);
        return redirect()->back()->with('success', 'Payroll finalized');
    }

    public function slip(Payroll $payroll)
    {
        return view('hrmanagement::payroll.slip', compact('payroll'));
    }
}
