<?php

namespace Plugins\HRManagement\app\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $table = 'leave_types';
    protected $fillable = ['name', 'days_per_year', 'is_paid', 'is_active'];
    protected $casts = ['is_paid' => 'boolean', 'is_active' => 'boolean'];

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }
}
