<?php

namespace Fuelviews\SabHeroArticles\Filament\Resources;

use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Fuelviews\SabHeroArticles\Filament\Resources\PageResource\Pages\CreatePage;
use Fuelviews\SabHeroArticles\Filament\Resources\PageResource\Pages\EditPage;
use Fuelviews\SabHeroArticles\Filament\Resources\PageResource\Pages\ListPages;
use Fuelviews\SabHeroArticles\Filament\Resources\PageResource\Pages\ViewPage;
use Fuelviews\SabHeroArticles\Models\Page;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'SEO';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return (string) Page::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Page::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->description(function ($record) {
                        return Str::limit($record->description, 80);
                    })
                    ->searchable()
                    ->sortable()
                    ->limit(80),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Route')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\SpatieMediaLibraryImageColumn::make('feature_image')
                    ->label('Featured Image')
                    ->collection('page_feature_image')
                    ->circular(),

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
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ReplicateAction::make()
                        ->color('info')
                        ->beforeReplicaSaved(function (Page $replica): void {
                            $replica->title = $replica->title.' (Copy)';
                            $replica->slug = Str::slug($replica->title.' copy '.time());
                        })
                        ->afterReplicaSaved(function (Page $replica, Page $original): void {
                            // Copy media/images using Spatie's copyMedia method
                            $mediaItems = $original->getMedia('page_feature_image');
                            foreach ($mediaItems as $media) {
                                $media->copy($replica, 'page_feature_image');
                            }
                        })
                        ->successNotificationTitle('Page copied successfully'),
                    Tables\Actions\DeleteAction::make(),
                ])->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Page')
                ->schema([
                    TextEntry::make('title'),

                    TextEntry::make('slug')
                        ->label('Route'),

                    TextEntry::make('description')
                        ->label('Meta Description')
                        ->formatStateUsing(function ($state) {
                            return ucfirst($state);
                        })
                        ->columnSpanFull(),

                    SpatieMediaLibraryImageEntry::make('Featured Image')
                        ->label('Featured Image')
                        ->collection('page_feature_image')
                        ->columnSpanFull(),
                ])->columns(2)
                ->icon('heroicon-o-square-3-stack-3d'),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
            'view' => ViewPage::route('/{record}'),
        ];
    }
}
