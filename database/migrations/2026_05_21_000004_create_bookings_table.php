<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->decimal('total_price', 10, 2);
            $table->string('status')->default('pending'); // pending, confirmed, checked_in, checked_out, cancelled
            $table->string('payment_status')->default('unpaid'); // unpaid, paid, refunded
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
