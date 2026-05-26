<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use PDO;

class InstallController extends Controller
{
    public function showInstall()
    {
        $extensions = ['pdo', 'openssl', 'mbstring', 'tokenizer', 'xml', 'ctype', 'json', 'fileinfo'];
        $compatibilities = [
            'php' => [
                'passed' => version_compare(PHP_VERSION, '8.2.0', '>='),
                'version' => PHP_VERSION,
            ],
            'extensions' => [],
            'permissions' => [
                'storage' => [
                    'passed' => is_writable(storage_path()),
                    'path' => storage_path()
                ],
                'bootstrap/cache' => [
                    'passed' => is_writable(base_path('bootstrap/cache')),
                    'path' => base_path('bootstrap/cache')
                ],
            ]
        ];

        $hasFailures = !$compatibilities['php']['passed'];

        foreach ($extensions as $ext) {
            $passed = extension_loaded($ext);
            $compatibilities['extensions'][$ext] = ['passed' => $passed];
            if (!$passed) {
                $hasFailures = true;
            }
        }

        foreach ($compatibilities['permissions'] as $perm) {
            if (!$perm['passed']) {
                $hasFailures = true;
            }
        }

        return view('install', compact('compatibilities', 'hasFailures'));
    }

    public function testConnection(Request $request)
    {
        $request->validate([
            'db_connection' => 'required|in:sqlite,mysql',
            'db_database_sqlite' => 'required_if:db_connection,sqlite|string',
            'db_host' => 'required_if:db_connection,mysql|string',
            'db_port' => 'required_if:db_connection,mysql|string',
            'db_database_mysql' => ['required_if:db_connection,mysql', 'string', 'regex:/^[A-Za-z0-9_]+$/'],
            'db_username' => 'required_if:db_connection,mysql|string',
            'db_password' => 'nullable|string',
        ]);

        try {
            if ($request->db_connection === 'sqlite') {
                $path = base_path($request->db_database_sqlite);
                $dir = dirname($path);
                if (!File::exists($dir)) {
                    return response()->json([
                        'success' => false,
                        'message' => "The directory for the SQLite file does not exist: {$dir}"
                    ]);
                }
                // Try creating file if it doesn't exist
                if (!File::exists($path)) {
                    touch($path);
                }
                $db = new PDO("sqlite:{$path}");
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } else {
                $host = $request->db_host;
                $port = $request->db_port;
                $dbname = $request->db_database_mysql;
                $username = $request->db_username;
                $password = $request->db_password ?? '';

                // First try connecting to test server without database (to support DB auto-creation)
                $dsn = "mysql:host={$host};port={$port}";
                $db = new PDO($dsn, $username, $password, [
                    PDO::ATTR_TIMEOUT => 5,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]);
            }
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage()
            ]);
        }
    }

    public function processInstall(Request $request)
    {
        $request->validate([
            'db_connection' => 'required|in:sqlite,mysql',
            'db_database_sqlite' => 'required_if:db_connection,sqlite|string',
            'db_host' => 'required_if:db_connection,mysql|string',
            'db_port' => 'required_if:db_connection,mysql|string',
            'db_database_mysql' => ['required_if:db_connection,mysql', 'string', 'regex:/^[A-Za-z0-9_]+$/'],
            'db_username' => 'required_if:db_connection,mysql|string',
            'db_password' => 'nullable|string',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255',
            'admin_password' => 'required|string|min:8',
            'demo_content' => 'nullable|boolean',
        ]);

        try {
            $connection = $request->db_connection;

            if ($connection === 'sqlite') {
                $dbPath = base_path($request->db_database_sqlite);
                if (!File::exists($dbPath)) {
                    touch($dbPath);
                }
                
                // Override runtime config
                config([
                    'database.default' => 'sqlite',
                    'database.connections.sqlite.database' => $dbPath,
                ]);
            } else {
                $host = $request->db_host;
                $port = $request->db_port;
                $dbname = $request->db_database_mysql;
                $username = $request->db_username;
                $password = $request->db_password ?? '';

                // Connect and create database if not exists
                $dsnNoDb = "mysql:host={$host};port={$port}";
                $pdo = new PDO($dsnNoDb, $username, $password, [
                    PDO::ATTR_TIMEOUT => 5,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]);
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

                // Override runtime config
                config([
                    'database.default' => 'mysql',
                    'database.connections.mysql.host' => $host,
                    'database.connections.mysql.port' => $port,
                    'database.connections.mysql.database' => $dbname,
                    'database.connections.mysql.username' => $username,
                    'database.connections.mysql.password' => $password,
                ]);
            }

            // Generate APP_KEY
            $appKey = 'base64:' . base64_encode(random_bytes(32));
            config(['app.key' => $appKey]);
            config(['app.demo_content' => $request->boolean('demo_content')]);

            // Run migrations & seeds
            Artisan::call('migrate:fresh', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);

            // Replace default super admin with custom one
            User::where('role', 'super_admin')->delete();
            User::create([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->admin_password),
                'role' => 'super_admin',
                'status' => 'active',
            ]);

            // Write the .env file from .env.example
            $examplePath = base_path('.env.example');
            $envPath = base_path('.env');
            if (File::exists($examplePath)) {
                $content = File::get($examplePath);
                
                // Replace variables
                $content = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=' . $appKey, $content);
                if (str_contains($content, 'APP_DEMO_CONTENT=')) {
                    $content = preg_replace('/APP_DEMO_CONTENT=[^\n]*/', 'APP_DEMO_CONTENT=' . ($request->boolean('demo_content') ? 'true' : 'false'), $content);
                } else {
                    $content .= "\nAPP_DEMO_CONTENT=" . ($request->boolean('demo_content') ? 'true' : 'false') . "\n";
                }
                $content = preg_replace('/DB_CONNECTION=[^\n]*/', 'DB_CONNECTION=' . $connection, $content);
                
                if ($connection === 'sqlite') {
                    $content = preg_replace('/DB_DATABASE=[^\n]*/', 'DB_DATABASE=' . $request->db_database_sqlite, $content);
                } else {
                    $content = preg_replace('/DB_HOST=[^\n]*/', 'DB_HOST=' . $host, $content);
                    $content = preg_replace('/DB_PORT=[^\n]*/', 'DB_PORT=' . $port, $content);
                    $content = preg_replace('/DB_DATABASE=[^\n]*/', 'DB_DATABASE=' . $dbname, $content);
                    $content = preg_replace('/DB_USERNAME=[^\n]*/', 'DB_USERNAME=' . $username, $content);
                    $content = preg_replace('/DB_PASSWORD=[^\n]*/', 'DB_PASSWORD="' . $password . '"', $content);
                }

                File::put($envPath, $content);
            }

            // Write the installed file indicator
            File::put(storage_path('.installed'), now()->toDateTimeString());

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Installation failed: ' . $e->getMessage()
            ]);
        }
    }
}
˝˝