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
        Schema::create('api_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('token')->unique();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });

        Schema::create('webhook_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->string('secret');
            $table->text('events'); // Store JSON array of subscribed events
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('webhook_delivery_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webhook_subscription_id')
                  ->constrained('webhook_subscriptions')
                  ->onDelete('cascade');
            $table->string('event');
            $table->string('url');
            $table->text('payload');
            $table->integer('response_status')->nullable();
            $table->text('response_body')->nullable();
            $table->text('error')->nullable();
            $table->integer('duration_ms');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_delivery_logs');
        Schema::dropIfExists('webhook_subscriptions');
        Schema::dropIfExists('api_tokens');
    }
};
