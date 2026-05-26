<?php

use Plugins\HRManagement\app\Http\Controllers\{
    HRDashboardController,
    EmployeeController,
    DepartmentController,
    AttendanceController,
    LeaveController,
    PayrollController,
    PerformanceReviewController
};

// Admin/Super Admin Routes
Route::get('/', [HRDashboardController::class, 'dashboard'])->name('dashboard');

Route::resource('employees', EmployeeController::class);
Route::resource('departments', DepartmentController::class);

Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
Route::get('attendance/mark', [AttendanceController::class, 'mark'])->name('attendance.mark');
Route::post('attendance/store', [AttendanceController::class, 'store'])->name('attendance.store');
Route::get('attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');

Route::get('leaves', [LeaveController::class, 'index'])->name('leaves.index');
Route::get('leaves/request', [LeaveController::class, 'request'])->name('leaves.request');
Route::post('leaves/store', [LeaveController::class, 'store'])->name('leaves.store');
Route::post('leaves/{leave}/approve', [LeaveController::class, 'approve'])->name('leaves.approve');
Route::post('leaves/{leave}/reject', [LeaveController::class, 'reject'])->name('leaves.reject');

Route::get('payroll', [PayrollController::class, 'index'])->name('payroll.index');
Route::get('payroll/create', [PayrollController::class, 'create'])->name('payroll.create');
Route::post('payroll/store', [PayrollController::class, 'store'])->name('payroll.store');
Route::post('payroll/{payroll}/finalize', [PayrollController::class, 'finalize'])->name('payroll.finalize');
Route::get('payroll/{payroll}/slip', [PayrollController::class, 'slip'])->name('payroll.slip');

Route::get('performance', [PerformanceReviewController::class, 'index'])->name('performance.index');
Route::get('performance/create', [PerformanceReviewController::class, 'create'])->name('performance.create');
Route::post('performance/store', [PerformanceReviewController::class, 'store'])->name('performance.store');
Route::get('performance/{review}/edit', [PerformanceReviewController::class, 'edit'])->name('performance.edit');
Route::put('performance/{review}', [PerformanceReviewController::class, 'update'])->name('performance.update');
Route::delete('performance/{review}', [PerformanceReviewController::class, 'destroy'])->name('performance.destroy');
