<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('reviewed_by_id');
            $table->date('review_date');
            $table->integer('rating')->default(3); // 1-5
            $table->text('comments')->nullable();
            $table->text('strengths')->nullable();
            $table->text('areas_to_improve')->nullable();
            $table->text('goals')->nullable();
            $table->enum('status', ['draft', 'completed'])->default('draft');
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('reviewed_by_id')->references('id')->on('employees')->onDelete('cascade');
            $table->index('review_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('performance_reviews');
    }
};
