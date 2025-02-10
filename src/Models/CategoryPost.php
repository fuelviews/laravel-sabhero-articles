<?php

namespace Fuelviews\SabHeroBlog\Models;

use Database\Factories\CategoryPostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'category_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'post_id' => 'integer',
        'category_id' => 'integer',
    ];

    protected static function newFactory(): CategoryPostFactory
    {
        return new CategoryPostFactory();
    }

    public function getTable(): string
    {
        return config('sabhero-blog.tables.prefix') . 'category_' . config('sabhero-blog.tables.prefix') . 'post';
    }
}
