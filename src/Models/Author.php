<?php

namespace Fuelviews\SabHeroBlog\Models;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Author extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'slug',
        'bio',
        'links',
        'is_author',
    ];

    protected $casts = [
        'links' => 'array',
        'is_author' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('sabhero-blog.user.model'), config('sabhero-blog.user.foreign_key'));
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('avatar')
            ->withResponsiveImages()
            ->width(208)
            ->height(208)
            ->fit(Fit::Crop, 208, 208)
            ->performOnCollections('avatar');
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->user->getFirstMediaUrl('avatar') ?: $this->avatar_url ?? null;
    }

    public static function getForm(): array
    {
        return [
            Section::make()
                ->columns(2)
                ->schema([
                    Toggle::make('is_author')
                        ->label('Author')
                        ->inline(false)
                        ->columnSpanFull(),

                    Select::make('user_id')
                        ->label('User Name')
                        ->relationship('user', 'name'),

                    TextInput::make('slug')
                        ->maxLength(255)
                        ->columnSpan(1),

                    SpatieMediaLibraryFileUpload::make('avatar')
                        ->responsiveImages()
                        ->label('Avatar')
                        ->collection('avatar')
                        ->columnSpanFull(),

                    Grid::make(1)
                        ->schema([
                            Textarea::make('bio')
                                ->rows(5)
                                ->label('Bio')
                                ->columnSpan(1),

                            Repeater::make('links')
                                ->schema([
                                    Select::make('site')
                                        ->label('Site')
                                        ->options([
                                            'x' => 'X',
                                            'facebook' => 'Facebook',
                                            'linkedin' => 'Linkedin',
                                            'youtube' => 'Youtube',
                                            'github' => 'Github',
                                            'instagram' => 'Instagram',
                                            'threads' => 'Threads',
                                            'personal' => 'Personal',
                                            'business' => 'Business',
                                        ])
                                        ->required()
                                        ->columnSpanFull(),

                                    TextInput::make('link')
                                        ->label('Link')
                                        ->url()
                                        ->required(),
                                ])
                                ->label('')
                                ->addActionLabel('Add your links'),
                        ]),
                ]),
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($author) {
            if (empty($author->slug)) {
                $author->slug = Str::slug($author->user->name);
            }
        });
    }

    public function getNameAttribute(): ?string
    {
        return $this->user->name;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getTable(): string
    {
        return config('sabhero-blog.tables.prefix').'authors';
    }
}
