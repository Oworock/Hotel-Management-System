<?php

namespace Plugins\HRManagement\app\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $table = 'leaves';
    protected $fillable = ['employee_id', 'leave_type_id', 'from_date', 'to_date', 'days', 'reason', 'status', 'approved_by_id'];
    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(Employee::class, 'approved_by_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($leave) {
            if ($leave->from_date && $leave->to_date) {
                $leave->days = $leave->to_date->diffInDays($leave->from_date) + 1;
            }
        });
    }
}
