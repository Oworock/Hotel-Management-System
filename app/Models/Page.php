<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['title', 'slug', 'content', 'is_active', 'show_in_nav', 'meta_title', 'meta_description', 'meta_keywords'])]
class Page extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'show_in_nav' => 'boolean',
        ];
    }
}
