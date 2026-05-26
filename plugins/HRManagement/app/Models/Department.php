<?php

namespace Plugins\HRManagement\app\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'departments';
    protected $fillable = ['name', 'description', 'manager_id', 'budget', 'is_active'];
    protected $casts = ['is_active' => 'boolean', 'budget' => 'decimal:2'];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function designations()
    {
        return $this->hasMany(Designation::class);
    }
}
