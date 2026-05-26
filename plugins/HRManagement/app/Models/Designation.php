<?php

namespace Plugins\HRManagement\app\Models;

use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    protected $table = 'designations';
    protected $fillable = ['name', 'department_id', 'description', 'salary_range_min', 'salary_range_max', 'is_active'];
    protected $casts = ['is_active' => 'boolean', 'salary_range_min' => 'decimal:2', 'salary_range_max' => 'decimal:2'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
