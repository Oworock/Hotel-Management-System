<?php

namespace Plugins\HRManagement\app\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Plugins\HRManagement\app\Models\{PerformanceReview, Employee};

class PerformanceReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = PerformanceReview::with('employee', 'reviewedBy');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $reviews = $query->latest()->paginate(15);
        $employees = Employee::where('status', 'active')->get();

        return view('hrmanagement::performance.index', compact('reviews', 'employees'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->get();
        $reviewers = Employee::where('status', 'active')->get();
        return view('hrmanagement::performance.form', compact('employees', 'reviewers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'reviewed_by_id' => 'required|exists:employees,id',
            'review_date' => 'required|date',
            'rating' => 'required|integer|min:1|max:5',
            'comments' => 'nullable',
            'strengths' => 'nullable',
            'areas_to_improve' => 'nullable',
            'goals' => 'nullable',
        ]);

        PerformanceReview::create($validated);
        return redirect()->route('admin.hr.performance.index')->with('success', 'Review created successfully');
    }

    public function edit(PerformanceReview $review)
    {
        $employees = Employee::where('status', 'active')->get();
        $reviewers = Employee::where('status', 'active')->get();
        return view('hrmanagement::performance.form', compact('review', 'employees', 'reviewers'));
    }

    public function update(Request $request, PerformanceReview $review)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comments' => 'nullable',
            'strengths' => 'nullable',
            'areas_to_improve' => 'nullable',
            'goals' => 'nullable',
            'status' => 'required|in:draft,completed',
        ]);

        $review->update($validated);
        return redirect()->route('admin.hr.performance.index')->with('success', 'Review updated successfully');
    }

    public function destroy(PerformanceReview $review)
    {
        $review->delete();
        return redirect()->route('admin.hr.performance.index')->with('success', 'Review deleted');
    }
}
