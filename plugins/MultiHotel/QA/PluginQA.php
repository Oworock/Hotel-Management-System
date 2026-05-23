<?php

namespace Plugins\MultiHotel\QA;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Plugins\MultiHotel\Scopes\HotelScope;

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

        // 2. Default Property Seeding Audit
        $results[] = self::checkDefaultSeeding();

        // 3. Tenant Isolation Scoping Audit
        $results[] = self::checkTenantScoping();

        return $results;
    }

    /**
     * Check if the required tables and columns exist.
     */
    protected static function checkDatabaseSchema(): array
    {
        $passed = true;
        $messages = [];

        if (!Schema::hasTable('hotels')) {
            $passed = false;
            $messages[] = "Table 'hotels' is missing.";
        } else {
            $messages[] = "Table 'hotels' exists.";
        }

        $tables = ['users', 'rooms', 'room_types', 'bookings', 'payments', 'coupons', 'staff_shifts'];
        foreach ($tables as $tbl) {
            if (Schema::hasTable($tbl)) {
                if (!Schema::hasColumn($tbl, 'hotel_id')) {
                    $passed = false;
                    $messages[] = "Column 'hotel_id' is missing from '{$tbl}' table.";
                } else {
                    $messages[] = "Column 'hotel_id' exists in '{$tbl}' table.";
                }
            } else {
                $messages[] = "Core table '{$tbl}' does not exist (skip column check).";
            }
        }

        return [
            'name' => 'Database Schema Validation',
            'passed' => $passed,
            'message' => implode(' ', $messages),
        ];
    }

    /**
     * Verify that at least one hotel exists (the default property).
     */
    protected static function checkDefaultSeeding(): array
    {
        $passed = true;
        $messages = [];

        try {
            $count = DB::table('hotels')->count();
            if ($count > 0) {
                $messages[] = "Total of {$count} hotel properties found in database.";
            } else {
                $passed = false;
                $messages[] = "No hotels found in the database. Seeding missing.";
            }
        } catch (\Throwable $e) {
            $passed = false;
            $messages[] = "Error checking hotels table: " . $e->getMessage();
        }

        return [
            'name' => 'Default Property Seeding Audit',
            'passed' => $passed,
            'message' => implode(' ', $messages),
        ];
    }

    /**
     * Verify that the global HotelScope is registered on target Models.
     */
    protected static function checkTenantScoping(): array
    {
        $passed = true;
        $messages = [];
        $scopedModels = [
            \App\Models\Room::class,
            \App\Models\RoomType::class,
            \App\Models\Booking::class,
            \App\Models\Payment::class,
            \App\Models\User::class,
            \App\Models\Coupon::class,
            \App\Models\StaffShift::class,
        ];

        foreach ($scopedModels as $modelClass) {
            if (class_exists($modelClass)) {
                $instance = new $modelClass();
                $hasScope = false;
                
                foreach ($instance->getGlobalScopes() as $scopeIdentifier => $scope) {
                    if ($scope instanceof HotelScope || $scopeIdentifier === HotelScope::class) {
                        $hasScope = true;
                        break;
                    }
                }

                if ($hasScope) {
                    $messages[] = "HotelScope is active on " . class_basename($modelClass) . ".";
                } else {
                    $passed = false;
                    $messages[] = "HotelScope is NOT registered on " . class_basename($modelClass) . ".";
                }
            } else {
                $messages[] = "Model " . $modelClass . " does not exist (skip).";
            }
        }

        return [
            'name' => 'Tenant Isolation Scoping Audit',
            'passed' => $passed,
            'message' => implode(' ', $messages),
        ];
    }
}
