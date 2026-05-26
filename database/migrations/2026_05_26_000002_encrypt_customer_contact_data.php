<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'users' => ['phone', 'nationality'],
            'orders' => ['customer_name', 'customer_email', 'customer_phone'],
            'notification_manager_recipients' => ['email', 'phone'],
        ] as $table => $columns) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            DB::table($table)
                ->orderBy('id')
                ->chunk(100, function ($rows) use ($table, $columns) {
                    foreach ($rows as $row) {
                        $updates = [];

                        foreach ($columns as $column) {
                            if (!property_exists($row, $column) || $row->{$column} === null || $row->{$column} === '') {
                                continue;
                            }

                            $value = (string) $row->{$column};
                            if ($this->isLaravelEncryptedPayload($value)) {
                                continue;
                            }

                            $updates[$column] = Crypt::encryptString($value);
                        }

                        if ($updates !== []) {
                            DB::table($table)->where('id', $row->id)->update($updates);
                        }
                    }
                });
        }
    }

    public function down(): void
    {
        // Contact-data encryption is intentionally one-way.
    }

    private function isLaravelEncryptedPayload(string $value): bool
    {
        $decoded = json_decode(base64_decode($value, true) ?: '', true);

        return is_array($decoded)
            && array_key_exists('iv', $decoded)
            && array_key_exists('value', $decoded)
            && array_key_exists('mac', $decoded);
    }
};
