<?php

namespace Plugins\HRManagement\app\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Plugins\HRManagement\app\Models\{Department, Employee};

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with('employees', 'manager')->paginate(15);
        return view('hrmanagement::departments.index', compact('departments'));
    }

    public function create()
    {
        $managers = Employee::where('status', 'active')->get();
        return view('hrmanagement::departments.form', compact('managers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:departments',
            'description' => 'nullable',
            'manager_id' => 'nullable|exists:employees,id',
            'budget' => 'nullable|numeric',
        ]);

        Department::create($validated);
        return redirect()->route('admin.hr.departments.index')->with('success', 'Department created successfully');
    }

    public function edit(Department $department)
    {
        $managers = Employee::where('status', 'active')->get();
        return view('hrmanagement::departments.form', compact('department', 'managers'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|unique:departments,name,' . $department->id,
            'description' => 'nullable',
            'manager_id' => 'nullable|exists:employees,id',
            'budget' => 'nullable|numeric',
        ]);

        $department->update($validated);
        return redirect()->route('admin.hr.departments.index')->with('success', 'Department updated successfully');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('admin.hr.departments.index')->with('success', 'Department deleted successfully');
    }
}
