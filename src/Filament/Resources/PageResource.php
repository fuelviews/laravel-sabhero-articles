<?php

namespace Fuelviews\SabHeroBlog\Filament\Resources;

use Fuelviews\SabHeroBlog\Filament\Resources\PageResource\Pages\EditPage;
use Fuelviews\SabHeroBlog\Filament\Resources\PageResource\Pages\ListPages;
use Fuelviews\SabHeroBlog\Filament\Resources\PageResource\Pages\ViewPage;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Fuelviews\SabHeroBlog\Models\Page;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'SEO';

    protected static ?int $navigationSort = 1;

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
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('slug')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\SpatieMediaLibraryImageColumn::make('Featured Image')
                    ->collection('page_feature_image')
                    ->circular(),

                Tables\Columns\TextColumn::make('meta_description')
                    ->formatStateUsing(function ($state) {
                        return ucfirst($state);
                    })
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
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

                    TextEntry::make('slug'),

                    TextEntry::make('meta_description')
                        ->formatStateUsing(function ($state) {
                            return ucfirst($state);
                        })
                    ->columnSpanFull(),

                    SpatieMediaLibraryImageEntry::make('Featured Image')
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
            'edit' => EditPage::route('/{record}/edit'),
            'view' => ViewPage::route('/{record}'),
        ];
    }
}
