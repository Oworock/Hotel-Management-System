<?php

use Plugins\HRManagement\app\Http\Controllers\StaffHRController;

Route::get('staff-profile', [StaffHRController::class, 'profile'])->name('staff.profile');
Route::get('staff-leave', [StaffHRController::class, 'leaves'])->name('staff.leaves');
Route::get('staff-attendance', [StaffHRController::class, 'attendance'])->name('staff.attendance');
Route::get('staff-payslips', [StaffHRController::class, 'payslips'])->name('staff.payslips');
