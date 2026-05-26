<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('api_tokens')) {
            DB::table('api_tokens')
                ->select(['id', 'token'])
                ->orderBy('id')
                ->chunk(100, function ($tokens) {
                    foreach ($tokens as $token) {
                        if (!is_string($token->token) || preg_match('/\A[a-f0-9]{64}\z/i', $token->token)) {
                            continue;
                        }

                        DB::table('api_tokens')
                            ->where('id', $token->id)
                            ->update(['token' => hash('sha256', $token->token)]);
                    }
                });
        }

        if (Schema::hasTable('settings')) {
            DB::table('settings')
                ->select(['id', 'key', 'value'])
                ->whereNotNull('value')
                ->orderBy('id')
                ->chunk(100, function ($settings) {
                    foreach ($settings as $setting) {
                        if (!Setting::isSecretKey($setting->key) || $setting->value === '' || str_starts_with((string) $setting->value, 'enc:')) {
                            continue;
                        }

                        DB::table('settings')
                            ->where('id', $setting->id)
                            ->update(['value' => 'enc:' . Crypt::encryptString((string) $setting->value)]);
                    }
                });
        }

        foreach ([
            'webhook_subscriptions' => ['secret'],
            'channels' => ['api_key', 'api_secret'],
            'ota_channels' => ['api_key', 'api_secret'],
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
        // Secret hardening is intentionally one-way.
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
