<?php

namespace Plugins\HRManagement\app\Models;

use Illuminate\Database\Eloquent\Model;

class HRDocument extends Model
{
    protected $table = 'hr_documents';
    protected $fillable = ['employee_id', 'document_type', 'file_path', 'expiry_date'];
    protected $casts = ['expiry_date' => 'date'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isExpiringSoon()
    {
        return $this->expiry_date && $this->expiry_date->lessThanOrEqualTo(now()->addDays(30));
    }
}
