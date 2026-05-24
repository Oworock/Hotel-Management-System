<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('guest_id');
            $table->integer('overall_rating')->default(5);
            $table->integer('cleanliness_rating')->default(5);
            $table->integer('comfort_rating')->default(5);
            $table->integer('service_rating')->default(5);
            $table->integer('value_rating')->default(5);
            $table->text('comment')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_published')->default(false);
            $table->boolean('would_recommend')->default(true);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->foreign('guest_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
