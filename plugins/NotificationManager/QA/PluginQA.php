<?php

namespace Plugins\NotificationManager\QA;

use Illuminate\Support\Facades\Schema;
use Plugins\NotificationManager\Models\NotificationTemplate;
use Plugins\NotificationManager\Services\NotificationManagerService;

class PluginQA
{
    public static function run(): array
    {
        return [
            self::checkRequiredFiles(),
            self::checkSchema(),
            self::checkTemplateCatalog(),
            self::checkStandaloneBoundary(),
        ];
    }

    private static function checkRequiredFiles(): array
    {
        $files = [
            'plugin.json',
            'NotificationManagerServiceProvider.php',
            'routes/web.php',
            'Services/NotificationManagerService.php',
            'Http/Controllers/NotificationManagerController.php',
            'resources/views/notifications/index.blade.php',
        ];

        $missing = [];
        foreach ($files as $file) {
            if (!file_exists(base_path('plugins/NotificationManager/' . $file))) {
                $missing[] = $file;
            }
        }

        return [
            'name' => 'Plugin Package Integrity',
            'passed' => empty($missing),
            'message' => empty($missing) ? 'All required standalone plugin files are present.' : 'Missing files: ' . implode(', ', $missing),
        ];
    }

    private static function checkSchema(): array
    {
        $tables = [
            'notification_manager_templates',
            'notification_manager_recipients',
            'notification_manager_preferences',
            'notification_manager_logs',
            'notification_manager_inbox',
        ];

        $missing = [];
        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                $missing[] = $table;
            }
        }

        return [
            'name' => 'Notification Schema Readiness',
            'passed' => empty($missing),
            'message' => empty($missing) ? 'All notification tables are migrated.' : 'Pending migration tables: ' . implode(', ', $missing),
        ];
    }

    private static function checkTemplateCatalog(): array
    {
        $service = new NotificationManagerService();
        $events = array_keys(NotificationManagerService::EVENTS);
        $defaults = array_keys($service->defaultTemplates());
        $missingDefaults = array_diff($events, $defaults);

        if (!empty($missingDefaults)) {
            return [
                'name' => 'Template Catalog Coverage',
                'passed' => false,
                'message' => 'Missing default templates for: ' . implode(', ', $missingDefaults),
            ];
        }

        if (Schema::hasTable('notification_manager_templates')) {
            $stored = NotificationTemplate::pluck('event_key')->all();
            $missingStored = array_diff($events, $stored);
            if (!empty($missingStored)) {
                return [
                    'name' => 'Template Catalog Coverage',
                    'passed' => false,
                    'message' => 'Templates need seeding for: ' . implode(', ', $missingStored),
                ];
            }
        }

        return [
            'name' => 'Template Catalog Coverage',
            'passed' => true,
            'message' => 'Every supported event has an editable email/SMS template.',
        ];
    }

    private static function checkStandaloneBoundary(): array
    {
        $coreFiles = [
            app_path('Models/Booking.php'),
            app_path('Models/StaffShift.php'),
            app_path('Http/Controllers/StaffController.php'),
            app_path('Http/Controllers/ReceptionistController.php'),
        ];

        $pluginReferences = [];
        foreach ($coreFiles as $file) {
            if (file_exists($file) && str_contains(file_get_contents($file), 'NotificationManager')) {
                $pluginReferences[] = str_replace(base_path() . '/', '', $file);
            }
        }

        return [
            'name' => 'Standalone Plugin Boundary',
            'passed' => empty($pluginReferences),
            'message' => empty($pluginReferences)
                ? 'No core booking/staff files contain hard dependencies on NotificationManager.'
                : 'Core files contain plugin references: ' . implode(', ', $pluginReferences),
        ];
    }
}
