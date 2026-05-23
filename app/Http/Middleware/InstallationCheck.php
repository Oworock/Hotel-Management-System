<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InstallationCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->runningUnitTests() && !env('RUNNING_INSTALLATION_TEST')) {
            return $next($request);
        }

        $path = $request->path();

        // Allow static assets, images, and built resources
        if (
            str_starts_with($path, 'css') ||
            str_starts_with($path, 'js') ||
            str_starts_with($path, 'images') ||
            str_ends_with($path, '.css') ||
            str_ends_with($path, '.js') ||
            str_ends_with($path, '.png') ||
            str_ends_with($path, '.jpg') ||
            str_ends_with($path, '.svg') ||
            str_ends_with($path, '.ico') ||
            $request->is('up')
        ) {
            return $next($request);
        }

        $installedFile = storage_path('.installed');
        $envExists = file_exists(base_path('.env'));
        $isInstalled = file_exists($installedFile);

        // If visiting installation paths or piracy warning
        if ($request->is('install') || $request->is('install/*') || $request->is('pirated-copy')) {
            if ($isInstalled) {
                if ($request->is('pirated-copy')) {
                    return redirect('/');
                }
                
                // Pretend the installation routes do not exist
                abort(404);
            }
            return $next($request);
        }

        // Standard route requests
        if ($isInstalled) {
            if ($envExists) {
                // Happy path: proceed normally
                return $next($request);
            } else {
                // Configuration missing but marked as installed
                abort(503, 'Configuration error: The application configuration file is missing. Please restore the configuration file or contact the system administrator.');
            }
        } else {
            // Not installed (installed file is missing)
            if ($envExists) {
                // Pirated Copy! .env exists but installed file is missing
                return redirect()->route('pirated.copy');
            } else {
                // Fresh copy: redirect to installer
                return redirect()->route('install');
            }
        }
    }
}
