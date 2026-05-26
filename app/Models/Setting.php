<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Support\Facades\Crypt;

#[Fillable(['key', 'value'])]
class Setting extends Model
{
    public static function getValue(string $key, $default = null): ?string
    {
        try {
            $setting = self::where('key', $key)->first();
            if (!$setting) {
                return $default;
            }

            if (self::isSecretKey($key) && is_string($setting->value) && str_starts_with($setting->value, 'enc:')) {
                try {
                    return Crypt::decryptString(substr($setting->value, 4));
                } catch (\Throwable $e) {
                    return $default;
                }
            }

            return $setting->value;
        } catch (\Throwable $e) {
            return $default;
        }
    }

    public static function setValue(string $key, ?string $value): void
    {
        if (self::isSecretKey($key) && $value !== null && $value !== '') {
            $value = str_starts_with($value, 'enc:') ? $value : 'enc:' . Crypt::encryptString($value);
        }

        self::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function isSecretKey(string $key): bool
    {
        $key = strtolower($key);

        foreach (['secret', 'password', 'token', 'private_key', 'api_key', 'api_secret'] as $needle) {
            if (str_contains($key, $needle)) {
                return true;
            }
        }

        return false;
    }
}
