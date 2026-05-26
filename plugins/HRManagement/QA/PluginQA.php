<?php

namespace Plugins\HRManagement\QA;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class PluginQA
{
    /**
     * Run all QA compliance checks and return the results.
     *
     * @return array
     */
    public static function run(): array
    {
        $results = [];

        // 1. Schema Validation
        $results[] = self::checkDatabaseSchema();

        // 2. Default Seeding Audit
        $results[] = self::checkDefaultSeeding();

        // 3. Data Integrity Audit
        $results[] = self::checkDataIntegrity();

        return $results;
    }

    /**
     * Check if the required HR tables and columns exist.
     */
    protected static function checkDatabaseSchema(): array
    {
        $passed = true;
        $messages = [];

        $requiredTables = [
            'departments' => ['name', 'description', 'is_active'],
            'designations' => ['name', 'department_id', 'is_active'],
            'employees' => ['employee_code', 'first_name', 'last_name', 'department_id', 'designation_id', 'basic_salary', 'status'],
            'attendance' => ['employee_id', 'date', 'status'],
            'leave_types' => ['name', 'days_per_year', 'is_paid', 'is_active'],
            'leaves' => ['employee_id', 'leave_type_id', 'from_date', 'to_date', 'status'],
            'payroll' => ['employee_id', 'month', 'year', 'basic_salary', 'net_salary', 'status'],
            'performance_reviews' => ['employee_id', 'reviewed_by_id', 'rating', 'status'],
            'hr_documents' => ['employee_id', 'document_type', 'file_path'],
        ];

        foreach ($requiredTables as $table => $columns) {
            if (!Schema::hasTable($table)) {
                $passed = false;
                $messages[] = "Table '{$table}' is missing.";
            } else {
                $messages[] = "Table '{$table}' exists.";

                foreach ($columns as $column) {
                    if (!Schema::hasColumn($table, $column)) {
                        $passed = false;
                        $messages[] = "Column '{$column}' is missing from '{$table}' table.";
                    }
                }
            }
        }

        return [
            'name' => 'Database Schema Validation',
            'passed' => $passed,
            'message' => implode(' ', $messages),
        ];
    }

    /**
     * Verify that default seeding data exists (leave types).
     */
    protected static function checkDefaultSeeding(): array
    {
        $passed = true;
        $messages = [];

        try {
            $leaveTypeCount = DB::table('leave_types')->count();
            if ($leaveTypeCount > 0) {
                $messages[] = "Total of {$leaveTypeCount} leave types found in database.";
            } else {
                $passed = false;
                $messages[] = "No leave types found in the database. Run: php artisan db:seed --class=Plugins\\\\HRManagement\\\\database\\\\seeders\\\\LeaveTypeSeeder";
            }
        } catch (\Throwable $e) {
            $passed = false;
            $messages[] = "Error checking leave_types table: " . $e->getMessage();
        }

        return [
            'name' => 'Default Seeding Audit',
            'passed' => $passed,
            'message' => implode(' ', $messages),
        ];
    }

    /**
     * Verify data integrity and relationships.
     */
    protected static function checkDataIntegrity(): array
    {
        $passed = true;
        $messages = [];

        try {
            // Check if tables are not corrupted
            $tables = ['departments', 'designations', 'employees', 'attendance', 'leaves', 'leave_types', 'payroll', 'performance_reviews', 'hr_documents'];

            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    $count = DB::table($table)->count();
                    $messages[] = "Table '{$table}' has {$count} records.";
                }
            }

            // Check foreign key integrity for employees table
            $employees = DB::table('employees')->get();
            if ($employees->count() > 0) {
                foreach ($employees as $emp) {
                    if (!DB::table('departments')->where('id', $emp->department_id)->exists()) {
                        $passed = false;
                        $messages[] = "Employee {$emp->id} references non-existent department {$emp->department_id}.";
                    }
                    if (!DB::table('designations')->where('id', $emp->designation_id)->exists()) {
                        $passed = false;
                        $messages[] = "Employee {$emp->id} references non-existent designation {$emp->designation_id}.";
                    }
                }
            }

            // Check attendance record integrity
            $orphanedAttendance = DB::table('attendance')
                ->whereNotIn('employee_id', DB::table('employees')->select('id'))
                ->count();

            if ($orphanedAttendance > 0) {
                $passed = false;
                $messages[] = "Found {$orphanedAttendance} orphaned attendance records.";
            } else {
                $messages[] = "All attendance records have valid employee references.";
            }

            // Check leaves integrity
            $orphanedLeaves = DB::table('leaves')
                ->whereNotIn('employee_id', DB::table('employees')->select('id'))
                ->count();

            if ($orphanedLeaves > 0) {
                $passed = false;
                $messages[] = "Found {$orphanedLeaves} orphaned leave records.";
            } else {
                $messages[] = "All leave records have valid employee references.";
            }

        } catch (\Throwable $e) {
            $passed = false;
            $messages[] = "Error during data integrity check: " . $e->getMessage();
        }

        return [
            'name' => 'Data Integrity Audit',
            'passed' => $passed,
            'message' => implode(' ', $messages),
        ];
    }
}
