<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected array $targetTables = [
        'users',
        'rooms',
        'room_types',
        'bookings',
        'payments',
        'coupons',
        'staff_shifts'
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create hotels table
        if (!Schema::hasTable('hotels')) {
            Schema::create('hotels', function (Blueprint $table) {
                $table->id();
            $table->string('name');
            $table->string('address')->nullable();
            $table->text('map_embed_url')->nullable();
            $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Seed default hotel
        $hotelName = 'Aetheria Grand Hotel';
        $contactEmail = 'info@aetheriagrand.com';
        $contactPhone = '+1 (555) 123-4567';
        $address = '123 Main St, Paradise Island';

        // Attempt to fetch from settings table if it exists
        try {
            if (Schema::hasTable('settings')) {
                $dbHotelName = DB::table('settings')->where('key', 'hotel_name')->value('value');
                if ($dbHotelName) {
                    $hotelName = $dbHotelName;
                }
                $dbEmail = DB::table('settings')->where('key', 'contact_email')->value('value');
                if ($dbEmail) {
                    $contactEmail = $dbEmail;
                }
                $dbPhone = DB::table('settings')->where('key', 'contact_phone')->value('value');
                if ($dbPhone) {
                    $contactPhone = $dbPhone;
                }
                $dbAddress = DB::table('settings')->where('key', 'physical_address')->value('value');
                if ($dbAddress) {
                    $address = $dbAddress;
                }
            }
        } catch (\Throwable $e) {
            // Ignore settings fetch errors
        }

        // Ensure we have a default hotel seeded
        $defaultHotelId = DB::table('hotels')->where('name', $hotelName)->value('id');
        if (!$defaultHotelId) {
            $defaultHotelId = DB::table('hotels')->insertGetId([
                'name' => $hotelName,
                'address' => $address,
                'map_embed_url' => DB::table('settings')->where('key', 'map_address')->value('value'),
                'phone' => $contactPhone,
                'email' => $contactEmail,
                'description' => 'Default system hotel property created on MultiHotel migration.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Alter target tables to add hotel_id
        foreach ($this->targetTables as $tbl) {
            if (Schema::hasTable($tbl)) {
                if (!Schema::hasColumn($tbl, 'hotel_id')) {
                    Schema::table($tbl, function (Blueprint $table) {
                        $table->unsignedBigInteger('hotel_id')->nullable()->after('id');
                        $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('set null');
                    });
                }
                
                // Update existing records to default hotel_id
                DB::table($tbl)->whereNull('hotel_id')->update(['hotel_id' => $defaultHotelId]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        foreach ($this->targetTables as $tbl) {
            if (Schema::hasTable($tbl) && Schema::hasColumn($tbl, 'hotel_id')) {
                Schema::table($tbl, function (Blueprint $table) {
                    $table->dropForeign(['hotel_id']);
                    $table->dropColumn('hotel_id');
                });
            }
        }
        Schema::dropIfExists('hotels');
        Schema::enableForeignKeyConstraints();
    }
};
