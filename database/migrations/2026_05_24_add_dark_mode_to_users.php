<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'prefer_dark_mode')) {
                $table->boolean('prefer_dark_mode')->default(false)->after('email_verified_at');
            }
            if (!Schema::hasColumn('users', 'preferred_theme')) {
                $table->string('preferred_theme')->default('modern-minimal')->after('prefer_dark_mode');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'prefer_dark_mode')) {
                $table->dropColumn('prefer_dark_mode');
            }
            if (Schema::hasColumn('users', 'preferred_theme')) {
                $table->dropColumn('preferred_theme');
            }
        });
    }
};
