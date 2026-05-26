<?php

namespace Plugins\HRManagement\app\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Plugins\HRManagement\app\Models\{Employee, Department, Designation};

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with('department', 'designation');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('first_name', 'like', "%$search%")
                ->orWhere('last_name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('employee_code', 'like', "%$search%");
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $employees = $query->paginate(15);
        $departments = Department::where('is_active', true)->get();

        return view('hrmanagement::employees.index', compact('employees', 'departments'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        $designations = Designation::where('is_active', true)->get();
        return view('hrmanagement::employees.form', compact('departments', 'designations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_code' => 'required|unique:employees',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:employees',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'hired_date' => 'required|date',
            'basic_salary' => 'required|numeric',
        ]);

        Employee::create($validated);
        return redirect()->route('admin.hr.employees.index')->with('success', 'Employee created successfully');
    }

    public function show(Employee $employee)
    {
        return view('hrmanagement::employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $departments = Department::where('is_active', true)->get();
        $designations = Designation::where('is_active', true)->get();
        return view('hrmanagement::employees.form', compact('employee', 'departments', 'designations'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'basic_salary' => 'required|numeric',
        ]);

        $employee->update($validated);
        return redirect()->route('admin.hr.employees.show', $employee)->with('success', 'Employee updated successfully');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('admin.hr.employees.index')->with('success', 'Employee deleted successfully');
    }
}
