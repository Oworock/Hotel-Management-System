<?php

namespace Plugins\HRManagement\app\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employees';
    protected $fillable = [
        'user_id', 'department_id', 'designation_id', 'employee_code', 'first_name', 'last_name',
        'email', 'phone', 'dob', 'hired_date', 'employment_type', 'basic_salary', 'allowances',
        'deductions', 'status', 'address', 'emergency_contact'
    ];
    protected $casts = [
        'basic_salary' => 'decimal:2',
        'allowances' => 'decimal:2',
        'deductions' => 'decimal:2',
        'dob' => 'date',
        'hired_date' => 'date'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function payroll()
    {
        return $this->hasMany(Payroll::class);
    }

    public function reviews()
    {
        return $this->hasMany(PerformanceReview::class, 'employee_id');
    }

    public function documents()
    {
        return $this->hasMany(HRDocument::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getTodayAttendance()
    {
        return $this->attendance()->whereDate('created_at', now())->first();
    }
}
