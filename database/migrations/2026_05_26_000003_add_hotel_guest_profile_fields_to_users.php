<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'title')) {
                $table->string('title', 20)->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'gender')) {
                $table->string('gender', 30)->nullable()->after('nationality');
            }
            if (!Schema::hasColumn('users', 'date_of_birth')) {
                $table->text('date_of_birth')->nullable()->after('gender');
            }
            if (!Schema::hasColumn('users', 'country_of_residence')) {
                $table->text('country_of_residence')->nullable()->after('date_of_birth');
            }
            if (!Schema::hasColumn('users', 'address_line1')) {
                $table->text('address_line1')->nullable()->after('country_of_residence');
            }
            if (!Schema::hasColumn('users', 'address_line2')) {
                $table->text('address_line2')->nullable()->after('address_line1');
            }
            if (!Schema::hasColumn('users', 'city')) {
                $table->text('city')->nullable()->after('address_line2');
            }
            if (!Schema::hasColumn('users', 'state')) {
                $table->text('state')->nullable()->after('city');
            }
            if (!Schema::hasColumn('users', 'postal_code')) {
                $table->text('postal_code')->nullable()->after('state');
            }
            if (!Schema::hasColumn('users', 'id_type')) {
                $table->string('id_type', 50)->nullable()->after('postal_code');
            }
            if (!Schema::hasColumn('users', 'id_number')) {
                $table->text('id_number')->nullable()->after('id_type');
            }
            if (!Schema::hasColumn('users', 'emergency_contact_name')) {
                $table->text('emergency_contact_name')->nullable()->after('id_number');
            }
            if (!Schema::hasColumn('users', 'emergency_contact_relationship')) {
                $table->text('emergency_contact_relationship')->nullable()->after('emergency_contact_name');
            }
            if (!Schema::hasColumn('users', 'emergency_contact_phone')) {
                $table->text('emergency_contact_phone')->nullable()->after('emergency_contact_relationship');
            }
            if (!Schema::hasColumn('users', 'marketing_consent')) {
                $table->boolean('marketing_consent')->default(false)->after('emergency_contact_phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'gender',
                'date_of_birth',
                'country_of_residence',
                'address_line1',
                'address_line2',
                'city',
                'state',
                'postal_code',
                'id_type',
                'id_number',
                'emergency_contact_name',
                'emergency_contact_relationship',
                'emergency_contact_phone',
                'marketing_consent',
            ]);
        });
    }
};
