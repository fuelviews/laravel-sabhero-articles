<?php

namespace Fuelviews\SabHeroBlog\Models;

use Database\Factories\PostTagFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostTag extends Model
{
    use HasFactory;

    protected $table = 'blog_post_tag';

    protected $fillable = [
        'post_id',
        'tag_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'post_id' => 'integer',
        'tag_id' => 'integer',
    ];

    protected static function newFactory(): PostTagFactory
    {
        return new PostTagFactory();
    }

    public function getTable(): string
    {
        return config('sabhero-blog.tables.prefix') . 'post_' . config('sabhero-blog.tables.prefix') . 'tag';
    }
}
