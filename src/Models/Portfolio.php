<?php

namespace Fuelviews\SabHeroBlog\Models;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Portfolio extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'title',
        'description',
        'spacing',
        'order',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public static function getForm(): array
    {
        return [
            Forms\Components\Grid::make()
            ->schema([
            Forms\Components\Section::make('Portfolio Item')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\MarkdownEditor::make('description')
                        ->required()
                        ->columnSpanFull(),

                    Forms\Components\Select::make('spacing')
                        ->options([
                            'yes' => 'Top and Bottom',
                            'no' => 'No Spacing',
                            'top' => 'Top Only',
                            'bottom' => 'Bottom Only',
                        ])
                        ->default('yes')
                        ->required(),

                    Forms\Components\TextInput::make('order')
                        ->numeric()
                        ->default(0),

                    Forms\Components\Toggle::make('is_published')
                        ->label('Published')
                        ->inline(false)
                        ->default(true),
                ])
                ->columnSpan(['lg' => 2]),

            Forms\Components\Section::make('Images')
                ->schema([
                    SpatieMediaLibraryFileUpload::make('before_image')
                        ->collection('before_image')
                        ->label('Before Image')
                        ->required()
                        ->image()
                        ->imageEditor()
                        ->imageResizeMode('cover')
                        ->preserveFilenames(),

                    SpatieMediaLibraryFileUpload::make('after_image')
                        ->collection('after_image')
                        ->label('After Image')
                        ->required()
                        ->image()
                        ->imageEditor()
                        ->imageResizeMode('cover')
                        ->preserveFilenames(),
                ])
                ->columnSpan(['lg' => 2]),
            ])->columns(4)
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('before_image')
            ->singleFile();

        $this->addMediaCollection('after_image')
            ->singleFile();
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')
            ->width(300)
            ->height(300)
            ->sharpen(10);

        $this->addMediaConversion('preview')
            ->width(800)
            ->height(600);
    }

    public function getBeforeImageAttribute()
    {
        return $this->getFirstMediaUrl('before_image');
    }

    public function getAfterImageAttribute()
    {
        return $this->getFirstMediaUrl('after_image');
    }
}
