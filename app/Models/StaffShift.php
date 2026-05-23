<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['user_id', 'clock_in_at', 'clock_out_at', 'duration_minutes'])]
class StaffShift extends Model
{
    protected function casts(): array
    {
        return [
            'clock_in_at' => 'datetime',
            'clock_out_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
