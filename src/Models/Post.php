<?php

namespace Fuelviews\SabHeroBlog\Models;

use Fuelviews\SabHeroBlog\Enums\MetroType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Set;
use Fuelviews\SabHeroBlog\Enums\PostStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Spatie\FilamentMarkdownEditor\MarkdownEditor;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Post extends Model implements hasMedia
{
    use InteractsWithMedia, HasSEO;

    protected $fillable = [
        'title',
        'slug',
        'sub_title',
        'body',
        'status',
        'published_at',
        'scheduled_for',
        'feature_image_alt_text',
        'user_id',
        'state_id',
        'city_id',
    ];

    protected array $dates = [
        'scheduled_for',
    ];

    protected $casts = [
        'id' => 'integer',
        'published_at' => 'datetime',
        'scheduled_for' => 'datetime',
        'status' => PostStatus::class,
        'user_id' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saved(static function ($post) {
            $post->metros()->detach();

            if ($post->state_id) {
                $post->metros()->attach($post->state_id, ['type' => MetroType::STATE->value]);
            }

            if ($post->city_id) {
                $post->metros()->attach($post->city_id, ['type' => MetroType::CITY->value]);
            }
        });
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, config('sabhero-blog.tables.prefix').'category_'.config('sabhero-blog.tables.prefix').'post');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class,config('sabhero-blog.tables.prefix').'post_'.config('sabhero-blog.tables.prefix').'tag');
    }

    public function metros(): BelongsToMany
    {
        return $this->belongsToMany(
            Metro::class,
            config('sabhero-blog.tables.prefix') . 'metro_' . config('sabhero-blog.tables.prefix') . 'post',
            'post_id',
            'metro_id'
        )->withPivot('type')
            ->withTimestamps();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('sabhero-blog.user.model'), config('sabhero-blog.user.foreign_key'));
    }

    public function isNotPublished(): bool
    {
        return ! $this->isStatusPublished();
    }

    public function scopePublished(Builder $query)
    {
        return $query->where('status', PostStatus::PUBLISHED)->latest('published_at');
    }

    public function scopeScheduled(Builder $query)
    {
        return $query->where('status', PostStatus::SCHEDULED)->latest('scheduled_for');
    }

    public function scopePending(Builder $query)
    {
        return $query->where('status', PostStatus::PENDING)->latest('created_at');
    }

    public function formattedPublishedDate()
    {
        return $this->published_at?->format('d M Y');
    }

    public function isScheduled()
    {
        return $this->status === PostStatus::SCHEDULED;
    }

    public function isStatusPublished()
    {
        return $this->status === PostStatus::PUBLISHED;
    }

    public function relatedPosts($take = 3)
    {
        return $this->whereHas('categories', function ($query) {
            $query->whereIn(config('sabhero-blog.tables.prefix').'categories.id', $this->categories->pluck('id'))
                ->whereNotIn(config('sabhero-blog.tables.prefix').'posts.id', [$this->id]);
        })->published()->with('user')->take($take)->get();
    }

    public function getDynamicSEOData(): SEOData
    {
        return new SEOData(
            title: $this->title,
            description: ($this->sub_title ?? $this->excerptHtml()),
            author: $this->user->name,
            image: $this->getFirstMediaUrl('post_feature_image'),
            locale: app()->getLocale(),
        );
    }

    public function excerptHtml(): string
    {
        $html = Str::markdown($this->body);
        $html = preg_replace('/<h1[^>]*>.*?<\/h1>/', '', $html, 1);
        $plainText = strip_tags($html);

        return Str::limit($plainText, 304);
    }

    public static function getForm(): array
    {
        return [
            Section::make('Blog Details')
                ->schema([
                    Fieldset::make('Titles')
                        ->schema([
                            TextInput::make('title')
                                ->live(true)
                                ->afterStateUpdated(fn (Set $set, ?string $state) => $set(
                                    'slug',
                                    Str::slug($state)
                                ))
                                ->required()
                                ->unique(config('sabhero-blog.tables.prefix').'posts', 'title', null, 'id')
                                ->maxLength(255),

                            TextInput::make('slug')
                                ->required()
                                ->maxLength(255),

                            Textarea::make('sub_title')
                                ->maxLength(255)
                                ->columnSpanFull(),

                            Select::make('state_id')
                                ->label('State')
                                ->options(Metro::where('type', MetroType::STATE->value)->pluck('name', 'id'))
                                ->searchable()
                                ->reactive()
                                ->afterStateUpdated(function (callable $get, callable $set) {
                                    $set('city_id', null);
                                })
                                ->nullable()
                                ->placeholder('Select a State')
                                ->createOptionForm(Metro::getForm())
                                ->createOptionUsing(function (array $data) {
                                    $metro = Metro::create([
                                        'name' => $data['name'],
                                        'slug' => Str::slug($data['slug']),
                                        'type' => $data['type'] ?? MetroType::STATE->value,
                                        'parent_id' => $data['parent_id'] ?? null,
                                    ]);

                                    return $metro->id;
                                }),

                            Select::make('city_id')
                                ->label('City')
                                ->options(function (callable $get) {
                                    $stateId = $get('state_id');
                                    if (! $stateId) {
                                        return [];
                                    }
                                    return Metro::where('parent_id', $stateId)
                                        ->where('type', MetroType::CITY->value)
                                        ->pluck('name', 'id');
                                })
                                ->createOptionForm(Metro::getForm())
                                ->createOptionUsing(function (array $data, callable $get) {
                                    $stateId = $get('state_id');

                                    $metro = Metro::create([
                                        'name'      => $data['name'],
                                        'slug'      => Str::slug($data['name']),
                                        'type'      => MetroType::CITY->value,
                                        'parent_id' => $stateId,
                                    ]);

                                    return $metro->id;
                                })
                                ->searchable()
                                ->nullable()
                                ->placeholder('Select a City'),

                            Select::make('category_id')
                                ->multiple()
                                ->preload()
                                ->createOptionForm(Category::getForm())
                                ->searchable()
                                ->relationship('categories', 'name')
                                ->columnSpanFull(),

                            Select::make('tag_id')
                                ->multiple()
                                ->preload()
                                ->createOptionForm(Tag::getForm())
                                ->searchable()
                                ->relationship('tags', 'name')
                                ->columnSpanFull(),
                        ]),

                    MarkdownEditor::make('body')
                        ->fileAttachmentsVisibility('public')
                        ->required()
                        ->columnSpanFull(),

                    Section::make('Featured Image')
                        ->columns(1)
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('post_feature_image')
                                ->getUploadedFileNameForStorageUsing(function (UploadedFile $file): string {
                                    $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                                    $slug = Str::slug(strtolower($baseName), '_');
                                    $randomSuffix = Str::random(6);
                                    return "{$slug}_{$randomSuffix}.{$file->getClientOriginalExtension()}";
                                })
                                ->responsiveImages()
                                ->image()
                                ->collection('post_feature_image')
                                ->label('Feature Image'),

                            TextInput::make('feature_image_alt_text')
                                ->label('Alt Text')
                                ->required(),
                        ])->collapsible(),

                    Fieldset::make('Status')
                        ->schema([
                            ToggleButtons::make('status')
                                ->live()
                                ->inline()
                                ->options(PostStatus::class)
                                ->required(),

                            DateTimePicker::make('scheduled_for')
                                ->visible(function ($get) {
                                    return $get('status') === PostStatus::SCHEDULED->value;
                                })
                                ->required(function ($get) {
                                    return $get('status') === PostStatus::SCHEDULED->value;
                                })
                                ->minDate(now()->addMinutes(5))
                                ->native(false),
                        ]),

                    Select::make(config('sabhero-blog.user.foreign_key'))
                        ->relationship('user', config('sabhero-blog.user.columns.name'))
                        ->nullable(false)
                        ->default(auth()->id()),

                ]),
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(Metro::class, 'state_id')->where('type', MetroType::STATE->value);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(Metro::class, 'city_id')->where('type', MetroType::CITY->value);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('post_feature_image')
            ->withResponsiveImages()
            ->performOnCollections('post_feature_image');
    }

    public function stripMarkdown($content): string
    {
        $content = preg_replace('/^(#{1,6})\s*/m', '', $content);

        $content = preg_replace('/(\*\*|__)(.*?)\1/', '$2', $content);
        $content = preg_replace('/(\*|_)(.*?)\1/', '$2', $content);

        $content = preg_replace('/\[(.*?)\]\((.*?)\)/', '$1', $content);

        $content = preg_replace('/!\[(.*?)\]\((.*?)\)/', '', $content);

        $content = preg_replace('/`(.*?)`/', '$1', $content);

        $content = preg_replace('/^>\s*/m', '', $content);

        $content = preg_replace('/^(-{3,}|_{3,}|\*{3,})$/m', '', $content);

        $content = preg_replace('/^(\-|\*|\+)\s+/m', '', $content);

        $content = preg_replace('/^\d+\.\s+/m', '', $content);

        return trim($content);
    }

    public function getTitleAttribute($value): string
    {
        return Str::title($value);
    }

    public function getSubtitleAttribute($value): string
    {
        return Str::title($value);
    }

    public function excerpt(): string
    {
        return Str::limit($this->stripMarkdown($this->body), 304);
    }

    public function getTable(): string
    {
        return config('sabhero-blog.tables.prefix') . 'posts';
    }
}
