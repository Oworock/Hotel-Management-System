<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hotels') && !Schema::hasColumn('hotels', 'map_embed_url')) {
            Schema::table('hotels', function (Blueprint $table) {
                $table->text('map_embed_url')->nullable()->after('address');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('hotels') && Schema::hasColumn('hotels', 'map_embed_url')) {
            Schema::table('hotels', function (Blueprint $table) {
                $table->dropColumn('map_embed_url');
            });
        }
    }
};
