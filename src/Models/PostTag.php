<?php

namespace Fuelviews\SabHeroArticles\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'tag_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'post_id' => 'integer',
        'tag_id' => 'integer',
    ];

    public function getTable(): string
    {
        return config('sabhero-articles.tables.prefix').'post_'.config('sabhero-articles.tables.prefix').'tag';
    }
}
