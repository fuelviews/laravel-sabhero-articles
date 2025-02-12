<?php

namespace Fuelviews\SabHeroBlog\Models;

use Fuelviews\SabHeroBlog\Enums\MetroType;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MetroPost extends Pivot
{
    protected $fillable = [
        'post_id',
        'parent_id',
        'type',
    ];
    protected $casts = [
        'id' => 'integer',
        'post_id' => 'integer',
        'parent_id' => 'integer',
        'type' => MetroType::class,
    ];

    public function getTable(): string
    {
        return config('sabhero-blog.tables.prefix') . 'metro_' . config('sabhero-blog.tables.prefix') . 'post';
    }
}
