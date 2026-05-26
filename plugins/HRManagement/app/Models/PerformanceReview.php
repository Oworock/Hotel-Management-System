<?php

namespace Plugins\HRManagement\app\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceReview extends Model
{
    protected $table = 'performance_reviews';
    protected $fillable = [
        'employee_id', 'reviewed_by_id', 'review_date', 'rating', 'comments',
        'strengths', 'areas_to_improve', 'goals', 'status'
    ];
    protected $casts = ['review_date' => 'date'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewedBy()
    {
        return $this->belongsTo(Employee::class, 'reviewed_by_id');
    }

    public function getRatingLabelAttribute()
    {
        $labels = [1 => 'Poor', 2 => 'Below Average', 3 => 'Average', 4 => 'Good', 5 => 'Excellent'];
        return $labels[$this->rating] ?? 'N/A';
    }
}
