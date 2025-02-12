<?php

namespace Fuelviews\SabHeroBlog\Models;

use Database\Factories\PageFactory;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Page extends Model implements HasMedia
{
    use HasFactory;
    use HasSEO;
    use InteractsWithMedia;

    protected $guarded = [
        'title',
        'slug',
        'description',
        'image'
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    protected static function newFactory(): PageFactory
    {
        return new PageFactory();
    }

    public function getSeoDataAttribute(): SEOData
    {
        return new SEOData(
            title: ucfirst($this->title ?? ''),
            description: ucfirst($this->meta_description ?? ''),
            image: $this->getFirstMediaUrl('page_feature_image'),
        );
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('feature_image')
            ->withResponsiveImages()
            ->performOnCollections('page_feature_image');
    }

    public static function getForm(): array
    {
        return [
            Section::make('SEO Metadata')
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->live(debounce: 500)
                        ->formatStateUsing(function ($state) {
                            return ucfirst($state);
                        }),

                    TextInput::make('slug')
                        ->unique('pages', 'slug', ignoreRecord: true),

                    Textarea::make('meta_description')
                        ->columnSpanFull(),

                    SpatieMediaLibraryFileUpload::make('feature_image')
                        ->conversion('feature_image')
                        ->responsiveImages()
                        ->collection('page_feature_image')
                        ->columnSpanFull(),
            ]),
        ];
    }
}


