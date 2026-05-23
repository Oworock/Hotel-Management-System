<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class InstallationTest extends TestCase
{
    protected string $envPath;
    protected string $envBakPath;
    protected string $installedPath;
    protected string $installedBakPath;

    protected function setUp(): void
    {
        parent::setUp();
        
        putenv('RUNNING_INSTALLATION_TEST=true');
        $this->envPath = base_path('.env');
        $this->envBakPath = base_path('.env.testing.backup');
        $this->installedPath = storage_path('.installed');
        $this->installedBakPath = storage_path('.installed.testing.backup');

        // Backup real .env if exists
        if (file_exists($this->envPath)) {
            rename($this->envPath, $this->envBakPath);
        }

        // Backup real installed if exists
        if (file_exists($this->installedPath)) {
            rename($this->installedPath, $this->installedBakPath);
        }
    }

    protected function tearDown(): void
    {
        putenv('RUNNING_INSTALLATION_TEST');

        // Restore .env if backup exists
        if (file_exists($this->envBakPath)) {
            if (file_exists($this->envPath)) {
                unlink($this->envPath);
            }
            rename($this->envBakPath, $this->envPath);
        }

        // Restore installed if backup exists
        if (file_exists($this->installedBakPath)) {
            if (file_exists($this->installedPath)) {
                unlink($this->installedPath);
            }
            rename($this->installedBakPath, $this->installedPath);
        } else {
            // Clean up any test-created installed file
            if (file_exists($this->installedPath)) {
                unlink($this->installedPath);
            }
        }

        parent::tearDown();
    }

    public function test_fresh_copy_without_env_and_without_installed_redirects_to_install()
    {
        // Ensure env and installed are missing
        if (file_exists($this->envPath)) {
            unlink($this->envPath);
        }
        if (file_exists($this->installedPath)) {
            unlink($this->installedPath);
        }

        $response = $this->get('/');
        $response->assertRedirect('/install');
    }

    public function test_unlicensed_copy_with_env_but_without_installed_redirects_to_pirated_copy()
    {
        // Create dummy env but ensure installed is missing
        File::put($this->envPath, 'APP_NAME="Testing"');
        if (file_exists($this->installedPath)) {
            unlink($this->installedPath);
        }

        $response = $this->get('/');
        $response->assertRedirect('/pirated-copy');

        // Verify the warning page displays Jambapp Limited and link
        $viewResponse = $this->get('/pirated-copy');
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Pirated Copy Detected');
        $viewResponse->assertSee('Jambapp Limited');
        $viewResponse->assertSee('www.jambapp.org');
    }

    public function test_installed_and_licensed_copy_proceeds_normally()
    {
        // Create dummy env and dummy installed
        File::put($this->envPath, 'APP_NAME="Testing"');
        File::put($this->installedPath, '2026-05-23');

        // Migrate the testing database so the home page tables exist
        $this->artisan('migrate');

        $response = $this->get('/');
        // Since we are not logged in, it should redirect to login or show the home page, but NOT redirect to /install or /pirated-copy
        $response->assertStatus(200);
    }

    public function test_cannot_access_install_routes_if_installed_file_exists()
    {
        // Create dummy installed
        File::put($this->installedPath, '2026-05-23');

        $response = $this->get('/install');
        $response->assertStatus(404);
    }

    public function test_server_compatibility_check_page_loads()
    {
        // Ensure installed is missing
        if (file_exists($this->installedPath)) {
            unlink($this->installedPath);
        }

        $response = $this->get('/install');
        $response->assertStatus(200);
        $response->assertSee('Server Compatibility Check');
        $response->assertSee('PHP Version');
    }

    public function test_database_connection_test_validates_sqlite()
    {
        // Ensure installed is missing
        if (file_exists($this->installedPath)) {
            unlink($this->installedPath);
        }

        $response = $this->postJson('/install/test-db', [
            'db_connection' => 'sqlite',
            'db_database_sqlite' => 'database/database.sqlite'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }
}
