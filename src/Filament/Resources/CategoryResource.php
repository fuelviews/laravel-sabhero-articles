<?php

namespace Fuelviews\SabHeroArticles\Filament\Resources;

use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Fuelviews\SabHeroArticles\Filament\Resources\CategoryResource\Pages\EditCategory;
use Fuelviews\SabHeroArticles\Filament\Resources\CategoryResource\Pages\ListCategories;
use Fuelviews\SabHeroArticles\Filament\Resources\CategoryResource\Pages\ViewCategory;
use Fuelviews\SabHeroArticles\Filament\Resources\CategoryResource\RelationManagers\PostsRelationManager;
use Fuelviews\SabHeroArticles\Models\Category;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';

    protected static ?string $navigationGroup = 'Article';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return (string) Category::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Category::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->sortable(),

                Tables\Columns\TextColumn::make('posts_count')
                    ->badge()
                    ->label('Posts Count')
                    ->counts('posts')
                    ->sortable(),

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
                        ->excludeAttributes(['posts_count'])
                        ->beforeReplicaSaved(function ($replica): void {
                            $replica->name = $replica->name.' (Copy)';
                            $replica->slug = Str::slug($replica->name.' copy '.time());
                        })
                        ->successNotificationTitle('Category copied successfully'),
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
            Section::make('Category')
                ->schema([
                    TextEntry::make('name'),

                    TextEntry::make('slug'),
                ])->columns(2)
                ->icon('heroicon-o-square-3-stack-3d'),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            PostsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategories::route('/'),
            // 'edit' => EditCategory::route('/{record}/edit'),
            'view' => ViewCategory::route('/{record}'),
        ];
    }
}
