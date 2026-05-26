<?php

namespace App\Http\Controllers;

use App\Models\Plugin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use ZipArchive;

class PluginController extends Controller
{
    public function index()
    {
        // Scan directory for plugins
        $pluginsPath = base_path('plugins');
        if (!File::exists($pluginsPath)) {
            File::makeDirectory($pluginsPath, 0755, true);
        }

        $directories = File::directories($pluginsPath);
        $scannedPlugins = [];

        foreach ($directories as $dir) {
            $jsonPath = $dir . '/plugin.json';
            if (File::exists($jsonPath)) {
                $metadata = json_decode(File::get($jsonPath), true);
                if (is_array($metadata) && isset($metadata['name'])) {
                    $scannedPlugins[$metadata['name']] = [
                        'name' => $metadata['name'],
                        'version' => $metadata['version'] ?? '1.0.0',
                        'description' => $metadata['description'] ?? '',
                        'service_provider' => $metadata['service_provider'] ?? '',
                        'path' => $dir,
                    ];
                }
            }
        }

        // Sync with database
        if (Schema::hasTable('plugins')) {
            // Delete DB records for plugins that no longer exist on disk
            Plugin::whereNotIn('name', array_keys($scannedPlugins))->delete();

            // Insert or update scanned plugins
            foreach ($scannedPlugins as $name => $meta) {
                Plugin::updateOrCreate(
                    ['name' => $name],
                    [
                        'version' => $meta['version'],
                        'description' => $meta['description'],
                        'service_provider' => $meta['service_provider'],
                        // Keep current is_enabled value or default to true for the first plugin
                        'is_enabled' => Plugin::where('name', $name)->first()->is_enabled ?? true,
                    ]
                );
            }

            // Regenerate manifest cache!
            \App\Providers\PluginServiceProvider::writeManifest();
        }

        $plugins = Plugin::all();
        return view('super_admin.plugins', compact('plugins'));
    }

    public function toggle(Plugin $plugin)
    {
        $plugin->is_enabled = !$plugin->is_enabled;
        $plugin->save();

        // Regenerate manifest cache!
        \App\Providers\PluginServiceProvider::writeManifest();

        // Clear application caches to force routes reload
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');

        $status = $plugin->is_enabled ? 'enabled' : 'disabled';
        return redirect()->back()->with('success', "Plugin '{$plugin->name}' has been {$status} successfully.");
    }

    public function runQA(Plugin $plugin)
    {
        $qaClass = "Plugins\\{$plugin->name}\\QA\\PluginQA";
        if (class_exists($qaClass) && method_exists($qaClass, 'run')) {
            try {
                $results = $qaClass::run();
            } catch (\Throwable $e) {
                $results = [[
                    'name' => 'QA Execution Exception',
                    'passed' => false,
                    'message' => 'An exception occurred during QA execution: ' . $e->getMessage()
                ]];
            }
        } else {
            $results = [[
                'name' => 'QA Suite Availability',
                'passed' => false,
                'message' => 'No standard compliance QA suite class found for this plugin.'
            ]];
        }

        return response()->json(['results' => $results]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'plugin_zip' => 'required|file|mimes:zip|max:20480', // limit 20MB
        ]);

        $zipFile = $request->file('plugin_zip');
        $zip = new ZipArchive();

        if ($zip->open($zipFile->getRealPath()) === true) {
            $tempExtractPath = storage_path('app/temp_plugin_' . uniqid());
            if (!File::exists($tempExtractPath)) {
                File::makeDirectory($tempExtractPath, 0755, true);
            }

            if (!$this->zipEntriesAreSafe($zip)) {
                $zip->close();
                File::deleteDirectory($tempExtractPath);
                return redirect()->back()->with('error', 'Invalid plugin zip: unsafe file paths detected.');
            }

            $zip->extractTo($tempExtractPath);
            $zip->close();

            // Locate plugin.json recursively
            $jsonFiles = File::allFiles($tempExtractPath);
            $pluginJsonFile = null;
            foreach ($jsonFiles as $file) {
                if ($file->getFilename() === 'plugin.json') {
                    $pluginJsonFile = $file;
                    break;
                }
            }

            if (!$pluginJsonFile) {
                File::deleteDirectory($tempExtractPath);
                return redirect()->back()->with('error', 'Invalid plugin zip: plugin.json not found.');
            }

            // Parse metadata
            $metadata = json_decode(File::get($pluginJsonFile->getRealPath()), true);
            if (!is_array($metadata) || !isset($metadata['name'])) {
                File::deleteDirectory($tempExtractPath);
                return redirect()->back()->with('error', 'Invalid plugin.json metadata.');
            }

            $pluginName = $metadata['name'];
            if (!$this->isSafePluginName($pluginName)) {
                File::deleteDirectory($tempExtractPath);
                return redirect()->back()->with('error', 'Invalid plugin.json metadata: plugin name must contain only letters, numbers, dashes, and underscores.');
            }

            $targetPath = base_path('plugins/' . $pluginName);

            // Clean up existing plugin directory if exists
            if (File::exists($targetPath)) {
                File::deleteDirectory($targetPath);
            }

            // Move extracted plugin folder to target path
            $sourceDir = dirname($pluginJsonFile->getRealPath());
            File::moveDirectory($sourceDir, $targetPath);

            // Clean up temp dir
            File::deleteDirectory($tempExtractPath);

            // Register in database
            Plugin::updateOrCreate(
                ['name' => $pluginName],
                [
                    'version' => $metadata['version'] ?? '1.0.0',
                    'description' => $metadata['description'] ?? '',
                    'service_provider' => $metadata['service_provider'] ?? '',
                    'is_enabled' => false, // default disabled on upload
                ]
            );

            // Regenerate manifest cache!
            \App\Providers\PluginServiceProvider::writeManifest();

            // Clear application caches to force routes reload
            \Illuminate\Support\Facades\Artisan::call('route:clear');
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            \Illuminate\Support\Facades\Artisan::call('cache:clear');

            return redirect()->back()->with('success', "Plugin '{$pluginName}' uploaded and registered successfully. Enable it to load features.");
        }

        return redirect()->back()->with('error', 'Failed to open the uploaded zip file.');
    }

    public function logo($name)
    {
        $name = basename($name);
        $pluginsPath = base_path("plugins/{$name}");

        // Look for logo.png, logo.jpg, logo.jpeg, logo.svg
        $extensions = ['png', 'jpg', 'jpeg', 'svg'];
        foreach ($extensions as $ext) {
            $logoPath = "{$pluginsPath}/logo.{$ext}";
            if (File::exists($logoPath)) {
                return response()->file($logoPath);
            }
        }

        // Fallback: Generate a beautiful SVG logo dynamically
        $initials = strtoupper(substr($name, 0, 2));
        
        // Dynamic colors based on plugin name
        $colors = [
            'EcommerceTuckShop' => ['from' => '#7c3aed', 'to' => '#db2777'],
            'ChannelManager' => ['from' => '#2563eb', 'to' => '#0d9488'],
            'MultiHotel' => ['from' => '#e11d48', 'to' => '#ea580c'],
        ];

        $grad = $colors[$name] ?? ['from' => '#4f46e5', 'to' => '#9333ea'];

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" width="100%" height="100%">
    <defs>
        <linearGradient id="grad-{$name}" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:{$grad['from']};stop-opacity:1" />
            <stop offset="100%" style="stop-color:{$grad['to']};stop-opacity:1" />
        </linearGradient>
    </defs>
    <rect width="200" height="200" rx="40" fill="url(#grad-{$name})" />
    <text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle" font-family="'Outfit', 'Inter', sans-serif" font-size="72" font-weight="900" fill="#ffffff" letter-spacing="-2">
        {$initials}
    </text>
</svg>
SVG;

        return response($svg)->header('Content-Type', 'image/svg+xml');
    }

    public function delete(Plugin $plugin)
    {
        if ($plugin->name === 'EcommerceTuckShop') {
            Schema::disableForeignKeyConstraints();
            Schema::dropIfExists('order_items');
            Schema::dropIfExists('orders');
            Schema::dropIfExists('products');
            Schema::enableForeignKeyConstraints();

            // Clean migration records to permit re-running migrations if re-uploaded/re-enabled
            \Illuminate\Support\Facades\DB::table('migrations')
                ->where('migration', 'like', '%_create_shop_and_restaurant_tables')
                ->delete();
        }

        if ($plugin->name === 'ChannelManager') {
            Schema::disableForeignKeyConstraints();
            Schema::dropIfExists('channel_sync_logs');
            Schema::dropIfExists('channel_room_mappings');
            Schema::dropIfExists('ota_channels');
            Schema::enableForeignKeyConstraints();

            // Clean migration records to permit re-running migrations if re-uploaded/re-enabled
            \Illuminate\Support\Facades\DB::table('migrations')
                ->where('migration', 'like', '%_create_channel_manager_tables')
                ->delete();
        }

        if ($plugin->name === 'MultiHotel') {
            Schema::disableForeignKeyConstraints();
            
            $tables = ['users', 'rooms', 'room_types', 'bookings', 'payments', 'coupons', 'staff_shifts'];
            foreach ($tables as $tbl) {
                if (Schema::hasTable($tbl) && Schema::hasColumn($tbl, 'hotel_id')) {
                    Schema::table($tbl, function ($table) {
                        $table->dropForeign(['hotel_id']);
                        $table->dropColumn('hotel_id');
                    });
                }
            }
            
            Schema::dropIfExists('hotels');
            Schema::enableForeignKeyConstraints();

            \Illuminate\Support\Facades\DB::table('migrations')
                ->where('migration', 'like', '%_create_multi_hotel_tables')
                ->delete();
        }

        if (!$this->isSafePluginName($plugin->name)) {
            return redirect()->back()->with('error', 'Unsafe plugin name. Delete the plugin record manually after reviewing the database.');
        }

        $targetPath = base_path('plugins/' . $plugin->name);
        if (!app()->environment('testing') && File::exists($targetPath)) {
            File::deleteDirectory($targetPath);
        }

        $plugin->delete();

        // Regenerate manifest cache!
        \App\Providers\PluginServiceProvider::writeManifest();

        // Clear application caches to force routes reload
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');

        return redirect()->back()->with('success', "Plugin '{$plugin->name}' and all associated files deleted successfully.");
    }

    private function isSafePluginName(?string $name): bool
    {
        return is_string($name) && preg_match('/\A[A-Za-z0-9_-]+\z/', $name) === 1;
    }

    private function zipEntriesAreSafe(ZipArchive $zip): bool
    {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (!is_string($name) || $name === '') {
                return false;
            }

            $normalized = str_replace('\\', '/', $name);
            if (str_starts_with($normalized, '/') || preg_match('#(^|/)\.\.(?:/|$)#', $normalized)) {
                return false;
            }
        }

        return true;
    }
}
