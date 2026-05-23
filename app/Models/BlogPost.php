<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogPost extends Model
{
    protected $table = 'blog_posts';
    protected $fillable = ['title', 'slug', 'content', 'excerpt', 'featured_image', 'author_id', 'published_at', 'is_published'];
    protected $casts = ['is_published' => 'boolean', 'published_at' => 'datetime'];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
