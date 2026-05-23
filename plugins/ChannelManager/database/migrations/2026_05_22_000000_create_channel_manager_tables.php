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
        Schema::create('ota_channels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('api_key')->nullable();
            $table->string('api_secret')->nullable();
            $table->string('hotel_id')->nullable();
            $table->boolean('is_connected')->default(false);
            $table->timestamps();
        });

        Schema::create('channel_room_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('room_type_id');
            $table->string('channel_name');
            $table->string('ota_room_id');
            $table->decimal('rate_multiplier', 4, 2)->default(1.00);
            $table->timestamps();

            $table->foreign('room_type_id')->references('id')->on('room_types')->onDelete('cascade');
        });

        Schema::create('channel_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('channel_name');
            $table->string('sync_type');
            $table->string('status');
            $table->text('message')->nullable();
            $table->text('payload')->nullable(); // stored as JSON text or serialized data
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channel_sync_logs');
        Schema::dropIfExists('channel_room_mappings');
        Schema::dropIfExists('ota_channels');
    }
};
