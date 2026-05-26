<?php

namespace Plugins\HRManagement\app\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance';
    protected $fillable = ['employee_id', 'date', 'check_in', 'check_out', 'duration_minutes', 'status', 'notes'];
    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'date' => 'date'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function calculateDuration()
    {
        if ($this->check_in && $this->check_out) {
            $this->duration_minutes = $this->check_out->diffInMinutes($this->check_in);
            $this->save();
        }
    }
}
