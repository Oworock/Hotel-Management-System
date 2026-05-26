<?php

namespace Plugins\HRManagement\database\seeders;

use Illuminate\Database\Seeder;
use Plugins\HRManagement\app\Models\LeaveType;

class LeaveTypeSeeder extends Seeder
{
    public function run()
    {
        $leaveTypes = [
            ['name' => 'Annual Leave', 'days_per_year' => 20, 'is_paid' => true, 'is_active' => true],
            ['name' => 'Sick Leave', 'days_per_year' => 10, 'is_paid' => true, 'is_active' => true],
            ['name' => 'Casual Leave', 'days_per_year' => 5, 'is_paid' => true, 'is_active' => true],
            ['name' => 'Maternity Leave', 'days_per_year' => 90, 'is_paid' => true, 'is_active' => true],
            ['name' => 'Paternity Leave', 'days_per_year' => 14, 'is_paid' => true, 'is_active' => true],
            ['name' => 'Unpaid Leave', 'days_per_year' => 0, 'is_paid' => false, 'is_active' => true],
        ];

        foreach ($leaveTypes as $type) {
            LeaveType::firstOrCreate(['name' => $type['name']], $type);
        }
    }
}
