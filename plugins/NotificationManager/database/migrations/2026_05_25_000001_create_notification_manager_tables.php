<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_manager_templates', function (Blueprint $table) {
            $table->id();
            $table->string('event_key')->unique();
            $table->string('name');
            $table->string('email_subject')->nullable();
            $table->longText('email_body')->nullable();
            $table->text('sms_body')->nullable();
            $table->boolean('send_email')->default(true);
            $table->boolean('send_sms')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('recipient_roles')->nullable();
            $table->timestamps();
        });

        Schema::create('notification_manager_recipients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('role')->nullable();
            $table->json('event_keys')->nullable();
            $table->boolean('wants_email')->default(true);
            $table->boolean('wants_sms')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('notification_manager_preferences', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->timestamps();
        });

        Schema::create('notification_manager_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_key');
            $table->string('channel');
            $table->string('recipient_name')->nullable();
            $table->string('recipient_email')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->string('status')->default('pending');
            $table->text('message')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_manager_logs');
        Schema::dropIfExists('notification_manager_preferences');
        Schema::dropIfExists('notification_manager_recipients');
        Schema::dropIfExists('notification_manager_templates');
    }
};
