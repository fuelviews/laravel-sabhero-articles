<?php

namespace Fuelviews\SabHeroBlog\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryPost extends Model
{
    protected $fillable = [
        'post_id',
        'category_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'post_id' => 'integer',
        'category_id' => 'integer',
    ];

    public function getTable(): string
    {
        return config('sabhero-blog.tables.prefix') . 'category_' . config('sabhero-blog.tables.prefix') . 'post';
    }
}
