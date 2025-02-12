<?php

namespace Fuelviews\SabHeroBlog\Models;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Fuelviews\SabHeroBlog\Enums\MetroType;

class Metro extends Model
{

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'type'
    ];

    protected $casts = [
        'id' => 'integer',
        'type' => MetroType::class,
    ];

    public function getNameAttribute($value): string
    {
        return Str::title($value);
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['name'] = strtolower($value);
    }

    public function getSlugAttribute($value): string
    {
        return Str::lower($value);
    }

    public function setSlugAttribute($value): void
    {
        $this->attributes['slug'] = strtolower($value);
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(
            Post::class,
            config('sabhero-blog.tables.prefix') . 'metro_' . config('sabhero-blog.tables.prefix') . 'post',
            'metro_id',
            'post_id'
        )->withPivot('type')
            ->withTimestamps();
    }

    public function scopeStates($query)
    {
        return $query->where('type', 'state');
    }

    public function scopeCities($query)
    {
        return $query->where('type', 'city');
    }

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        if ($childType === 'city') {
            return $this->children()
                ->where($field, $value)
                ->firstOrFail();
        }

        return parent::resolveChildRouteBinding($childType, $value, $field);
    }

    public static function getForm(): array
    {
        return [
            TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->live(debounce:500)
                ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state)))
                ->formatStateUsing(fn ($state) => Str::title($state)),

            TextInput::make('slug')
                ->required()
                ->maxLength(255),

            Select::make('type')
                ->options(collect(MetroType::cases())->mapWithKeys(fn ($type) => [$type->value => ucfirst($type->value)]))
                ->required()
                ->reactive(),

            Select::make('parent_id')
                ->label('Parent State')
                ->options(Metro::states()->pluck('name', 'id'))
                ->nullable()
                ->visible(fn ($get) => $get('type') === MetroType::CITY->value)
                ->required(fn ($get) => $get('type') === MetroType::CITY->value)
                ->placeholder('Select a parent state'),
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getTable(): string
    {
        return config('sabhero-blog.tables.prefix') . 'metros';
    }
}
