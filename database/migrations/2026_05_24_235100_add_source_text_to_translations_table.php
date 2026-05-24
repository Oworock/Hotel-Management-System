<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('translations') && !Schema::hasColumn('translations', 'source_text')) {
            Schema::table('translations', function (Blueprint $table) {
                $table->text('source_text')->nullable()->after('key');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('translations') && Schema::hasColumn('translations', 'source_text')) {
            Schema::table('translations', function (Blueprint $table) {
                $table->dropColumn('source_text');
            });
        }
    }
};
