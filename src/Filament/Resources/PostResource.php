<?php

namespace Fuelviews\SabHeroArticles\Filament\Resources;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
// use Fuelviews\SabHeroArticles\Enums\MetroType;
use Fuelviews\SabHeroArticles\Enums\PostStatus;
use Fuelviews\SabHeroArticles\Filament\Resources\PostResource\Pages\CreatePost;
use Fuelviews\SabHeroArticles\Filament\Resources\PostResource\Pages\EditPost;
use Fuelviews\SabHeroArticles\Filament\Resources\PostResource\Pages\ListPosts;
use Fuelviews\SabHeroArticles\Filament\Resources\PostResource\Pages\ViewPost;
use Fuelviews\SabHeroArticles\Filament\Resources\PostResource\Widgets\ArticlePostPublishedChart;
use Fuelviews\SabHeroArticles\Filament\Tables\Columns\UserAvatar;
use Fuelviews\SabHeroArticles\Models\Category;
use Fuelviews\SabHeroArticles\Models\Post;
use Fuelviews\SabHeroArticles\Models\Tag;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Csv\Reader;
use League\Csv\Writer;
use function parse_url;
use RuntimeException;

use ZipArchive;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-minus';

    protected static ?string $navigationGroup = 'Article';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 0;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationBadge(): ?string
    {
        return (string) Post::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Post::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->description(function (Post $record) {
                        return Str::limit($record->sub_title, 60);
                    })
                    ->searchable()->limit(20),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(function ($state) {
                        return $state->getColor();
                    }),

                Tables\Columns\SpatieMediaLibraryImageColumn::make('page_feature_image')
                    ->collection('post_feature_image')
                    ->label('Featured Image'),

                UserAvatar::make('user')
                    ->label('Author'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', config('sabhero-articles.user.columns.name'))
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->headerActions([
                Tables\Actions\ImportAction::make()
                    ->label('Import posts')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('gray')
                    ->form([
                        Forms\Components\FileUpload::make('zip_file')
                            ->label('Zip files only')
                            ->acceptedFileTypes(['application/zip'])
                            ->required(),
                    ])
                    ->action(fn (array $data) => static::importFromZip($data['zip_file']))
                    ->requiresConfirmation(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ReplicateAction::make()
                    ->beforeReplicaSaved(function (Post $replica, array $data): void {
                        $replica->title = $replica->title.' (Copy)';
                        $replica->slug = Str::slug($replica->title.' copy '.time());
                        $replica->status = \Fuelviews\SabHeroArticles\Enums\PostStatus::PUBLISHED;
                        $replica->published_at = null;
                        $replica->scheduled_for = null;
                    })
                    ->afterReplicaSaved(function (Post $replica, Post $original): void {
                        // Copy categories
                        $replica->categories()->sync($original->categories->pluck('id'));

                        // Copy tags
                        $replica->tags()->sync($original->tags->pluck('id'));

                        // Copy media/images
                        foreach ($original->getMedia('post_feature_image') as $media) {
                            $mediaPath = $media->getPath();
                            if (file_exists($mediaPath)) {
                                $replica->addMedia($mediaPath)
                                    ->usingName($media->name)
                                    ->usingFileName($media->file_name)
                                    ->preservingOriginal()
                                    ->toMediaCollection('post_feature_image');
                            }
                        }
                    })
                    ->successNotificationTitle('Post copied successfully'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('export_csv_and_images')
                        ->label('Export posts')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('gray')
                        ->action(fn ($records) => static::exportToZip($records))
                        ->requiresConfirmation(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Post Details')
                ->schema([
                    Fieldset::make('General Information')
                        ->schema([
                            TextEntry::make('title')
                                ->label('Title'),

                            TextEntry::make('slug')
                                ->label('Slug'),

                            TextEntry::make('sub_title')
                                ->label('Sub Title'),

                            TextEntry::make('body')
                                ->label('Content')
                                ->html()
                                ->columnSpanFull(),
                        ]),
                    Fieldset::make('Author and Meta')
                        ->schema([
                            TextEntry::make('user.name')
                                ->label('Author'),

                            TextEntry::make('created_at')
                                ->label('Created At')
                                ->dateTime(),

                            TextEntry::make('updated_at')
                                ->label('Last Updated')
                                ->dateTime(),
                        ]),
                    Fieldset::make('Categories and Tags')
                        ->schema([
                            TextEntry::make('categories.name')
                                ->label('Categories'),

                            TextEntry::make('tags.name')
                                ->label('Tags'),
                        ]),
                    Fieldset::make('Media')
                        ->schema([
                            SpatieMediaLibraryImageEntry::make('feature_image')
                                ->collection('post_feature_image')
                                ->label('Feature Image'),

                            TextEntry::make('feature_image_alt_text')
                                ->label('Alt Text'),
                        ]),
                    Fieldset::make('Publishing Information')
                        ->schema([
                            TextEntry::make('status')
                                ->label('Status')
                                ->badge()
                                ->color(fn ($state) => $state->getColor()),

                            TextEntry::make('published_at')
                                ->label('Published At')
                                ->visible(fn (Post $record) => $record->status === PostStatus::PUBLISHED),

                            TextEntry::make('scheduled_for')
                                ->label('Scheduled For')
                                ->visible(fn (Post $record) => $record->status === PostStatus::SCHEDULED),
                        ]),
                ]),
        ]);
    }

    public static function exportToZip($records)
    {
        $storagePath = storage_path('app/public/exports');
        if (! file_exists($storagePath)) {
            if (! mkdir($storagePath, 0777, true) && ! is_dir($storagePath)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $storagePath));
            }
        }

        $zipFilePath = $storagePath.'/posts_export.zip';
        $csvFilePath = $storagePath.'/posts.csv';

        $csv = Writer::createFromPath($csvFilePath, 'w+');
        $csv->insertOne([
            'ID',
            'Title',
            'Subtitle',
            'Content',
            'Slug',
            'Status',
            'Categories',
            'Tags',
            'Feature Image Alt Text',
            'Additional Media',
            'Author',
            'Published At',
            'Scheduled For',
            'Created At',
            'Updated At',
        ]);

        $zip = new ZipArchive;
        if ($zip->open($zipFilePath, ZipArchive::CREATE) !== true) {
            return back()->withErrors('Failed to create ZIP file.');
        }

        foreach ($records as $post) {
            $mediaUrls = [];
            foreach ($post->getMedia('post_feature_image') as $media) {
                $filePath = $media->getPath();
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, 'images/'.$media->file_name);
                    $mediaUrls[] = asset($media->getUrl());
                }
            }

            $csv->insertOne([
                $post->id ?? '',
                $post->title ?? '',
                $post->sub_title ?? '',
                $post->body ?? '',
                $post->slug ?? '',
                $post->status->value ?? '',
                $post->categories->pluck('name')->implode(',') ?? '',
                $post->tags->pluck('name')->implode(',') ?? '',
                $post->feature_image_alt_text ?? '',
                implode(', ', $mediaUrls) ?? '',
                $post->user?->name ?? '',
                $post->published_at ? $post->published_at->format('Y-m-d H:i:s') : '',
                $post->scheduled_for ? $post->scheduled_for->format('Y-m-d H:i:s') : '',
                $post->created_at ? $post->created_at->format('Y-m-d H:i:s') : '',
                $post->updated_at ? $post->updated_at->format('Y-m-d H:i:s') : '',
            ]);
        }

        $zip->addFile($csvFilePath, 'posts.csv');
        $zip->close();

        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }

    public static function importFromZip($zipFile)
    {
        $diskName = config('sabhero-articles.media.disk');

        $zipFilePath = Storage::disk($diskName)->path($zipFile);

        $extractFolder = 'unzipped_'.time();
        Storage::disk($diskName)->makeDirectory($extractFolder);
        $extractPath = Storage::disk($diskName)->path($extractFolder);

        $zip = new ZipArchive;
        $openResult = $zip->open($zipFilePath);
        if ($openResult === true) {
            $zip->extractTo($extractPath);
            $zip->close();
        } else {
            return back()->withErrors([
                'zip_file' => "Failed to extract ZIP file (Error code: $openResult).",
            ]);
        }

        $csvFilePath = null;
        foreach (scandir($extractPath) as $file) {
            if (\Str::endsWith($file, '.csv')) {
                $csvFilePath = $extractPath.'/'.$file;

                break;
            }
        }

        if (! $csvFilePath || ! file_exists($csvFilePath)) {
            return back()->withErrors(['zip_file' => 'No CSV file found in the extracted ZIP.']);
        }

        $csv = Reader::createFromPath($csvFilePath, 'r');
        $csv->setHeaderOffset(0);
        $records = $csv->getRecords();

        foreach ($records as $record) {
            $post = Post::updateOrCreate(
                ['slug' => $record['Slug']],
                [
                    'title' => $record['Title'] ?? '',
                    'sub_title' => $record['Subtitle'] ?? '',
                    'body' => $record['Content'] ?? '',
                    'status' => $record['Status'] ?? '',
                    'user_id' => optional(User::where('name', $record['Author'])->first())->id ?? '1',
                    'feature_image_alt_text' => $record['Feature Image Alt Text'] ?? '',
                    'published_at' => ! empty($record['Published At']) ? $record['Published At'] : null,
                    'scheduled_for' => ! empty($record['Scheduled For']) ? $record['Scheduled For'] : null,
                    'created_at' => ! empty($record['Created At']) ? $record['Created At'] : null,
                    'updated_at' => ! empty($record['Updated At']) ? $record['Updated At'] : null,
                ]
            );

            if (! empty($record['Categories'])) {
                $categoryNames = explode(',', $record['Categories']);
                $categoryIds = [];
                foreach ($categoryNames as $catName) {
                    $catName = trim($catName);
                    if (! $catName) {
                        continue;
                    }
                    $category = Category::firstOrCreate(
                        ['slug' => Str::slug($catName)],
                        ['name' => Str::lower($catName)]
                    );
                    $categoryIds[] = $category->id;
                }
                $post->categories()->sync($categoryIds, false);
            }

            if (! empty($record['Tags'])) {
                $tagNames = explode(',', $record['Tags']);
                $tagIds = [];
                foreach ($tagNames as $tagName) {
                    $tagName = trim($tagName);
                    if (! $tagName) {
                        continue;
                    }
                    $tag = Tag::firstOrCreate(
                        ['slug' => Str::slug($tagName)],
                        ['name' => Str::lower($tagName)]
                    );
                    $tagIds[] = $tag->id;
                }
                $post->tags()->sync($tagIds, false);
            }

            $imageUrls = explode(', ', $record['Additional Media']);
            foreach ($imageUrls as $imageUrl) {
                $imageName = basename(parse_url($imageUrl, PHP_URL_PATH));
                $imagePath = $extractPath.'/images/'.$imageName;

                if (file_exists($imagePath)) {
                    $post->clearMediaCollection('post_feature_image');

                    $post->addMedia($imagePath)->toMediaCollection('post_feature_image');
                }
            }
        }

        Storage::disk($diskName)->delete($zipFile);
        Storage::disk($diskName)->deleteDirectory($extractFolder);

        Notification::make()
            ->title('Posts imported successfully!')
            ->success()
            ->send();

        return back();
    }

    public static function getRecordTitle($record): string
    {
        return ucwords($record->title);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewPost::class,
            EditPost::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getWidgets(): array
    {
        return [
            ArticlePostPublishedChart::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
            'view' => ViewPost::route('/{record}'),
        ];
    }
}
