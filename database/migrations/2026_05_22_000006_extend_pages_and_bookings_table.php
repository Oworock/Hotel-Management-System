<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->integer('loyalty_points_redeemed')->default(0);
            $table->integer('loyalty_points_earned')->default(0);
            $table->decimal('loyalty_discount_amount', 8, 2)->default(0.00);
            $table->text('addons')->nullable(); // JSON string
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'meta_keywords']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['loyalty_points_redeemed', 'loyalty_points_earned', 'loyalty_discount_amount', 'addons']);
        });
    }
};
