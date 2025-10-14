<?php

namespace Fuelviews\SabHeroArticles\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Fuelviews\SabHeroArticles\Actions\PostExportAction;
use Fuelviews\SabHeroArticles\Actions\PostImportAction;
use Fuelviews\SabHeroArticles\Enums\PostStatus;
use Fuelviews\SabHeroArticles\Filament\Resources\PostResource\Pages\CreatePost;
use Fuelviews\SabHeroArticles\Filament\Resources\PostResource\Pages\EditPost;
use Fuelviews\SabHeroArticles\Filament\Resources\PostResource\Pages\ListPosts;
use Fuelviews\SabHeroArticles\Filament\Resources\PostResource\Pages\ViewPost;
use Fuelviews\SabHeroArticles\Filament\Resources\PostResource\Widgets\ArticlePostPublishedChart;
use Fuelviews\SabHeroArticles\Filament\Tables\Columns\UserAvatar;
use Fuelviews\SabHeroArticles\Models\Post;
use Illuminate\Support\Str;

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
                    ->searchable()
                    ->sortable()
                    ->limit(60),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->color(function ($state) {
                        return $state->getColor();
                    }),

                Tables\Columns\SpatieMediaLibraryImageColumn::make('page_feature_image')
                    ->collection('post_feature_image')
                    ->label('Featured Image'),

                UserAvatar::make('user')
                    ->label('Author')
                    ->sortable(),

                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime('M j, Y g:i A')
                    ->timezone('America/New_York')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y g:i A')
                    ->timezone('America/New_York')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('M j, Y g:i A')
                    ->timezone('America/New_York')
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ReplicateAction::make()
                        ->color('info')
                        ->excludeAttributes(['scheduled_for'])
                        ->beforeReplicaSaved(function (Post $replica, array $data): void {
                            $replica->title = $replica->title.' (Copy)';
                            $replica->slug = Str::slug($replica->title.' copy '.time());
                            $replica->scheduled_for = null;
                        })
                        ->afterReplicaSaved(function (Post $replica, Post $original): void {
                            // Copy categories
                            $replica->categories()->sync($original->categories->pluck('id'));

                            // Copy tags
                            $replica->tags()->sync($original->tags->pluck('id'));

                            // Copy media/images using Spatie's copyMedia method
                            $mediaItems = $original->getMedia('post_feature_image');
                            foreach ($mediaItems as $media) {
                                $media->copy($replica, 'post_feature_image');
                            }
                        })
                        ->successNotificationTitle('Post copied successfully'),
                    Tables\Actions\DeleteAction::make(),
                ])->iconButton(),
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
                                ->dateTime('M j, Y g:i A')
                                ->timezone('America/New_York'),

                            TextEntry::make('updated_at')
                                ->label('Last Updated')
                                ->dateTime('M j, Y g:i A')
                                ->timezone('America/New_York'),
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
                                ->label('Featured Image'),

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
                                ->dateTime('M j, Y g:i A')
                                ->timezone('America/New_York')
                                ->visible(fn (Post $record) => $record->status === PostStatus::PUBLISHED),

                            TextEntry::make('scheduled_for')
                                ->label('Scheduled For')
                                ->dateTime('M j, Y g:i A')
                                ->timezone('America/New_York')
                                ->visible(fn (Post $record) => $record->status === PostStatus::SCHEDULED),
                        ]),
                ]),
        ]);
    }

    /**
     * Export posts to ZIP file with CSV and images
     *
     * Delegates to PostExportAction for cleaner, testable code.
     */
    public static function exportToZip($records)
    {
        $exportAction = new PostExportAction;

        return $exportAction->execute($records);
    }

    /**
     * Import posts from ZIP file containing CSV and images
     *
     * Delegates to PostImportAction for cleaner, testable code.
     */
    public static function importFromZip($zipFile)
    {
        $importAction = new PostImportAction;
        $importAction->execute($zipFile);
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
