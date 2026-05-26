<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['user_id', 'name', 'token', 'last_used_at'])]
class ApiToken extends Model
{
    public static function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
