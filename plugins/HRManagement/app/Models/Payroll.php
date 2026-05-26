<?php

namespace Plugins\HRManagement\app\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $table = 'payroll';
    protected $fillable = [
        'employee_id', 'month', 'year', 'basic_salary', 'allowances', 'deductions',
        'tax', 'net_salary', 'status', 'payment_date', 'reference_number'
    ];
    protected $casts = [
        'basic_salary' => 'decimal:2',
        'allowances' => 'decimal:2',
        'deductions' => 'decimal:2',
        'tax' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'payment_date' => 'date'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function calculateNetSalary()
    {
        $gross = ($this->basic_salary ?? 0) + ($this->allowances ?? 0);
        $this->net_salary = $gross - ($this->deductions ?? 0) - ($this->tax ?? 0);
        return $this->net_salary;
    }
}
