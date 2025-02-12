<?php

namespace Fuelviews\SabHeroBlog\Filament\Resources;

use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Fuelviews\SabHeroBlog\Enums\MetroType;
use Fuelviews\SabHeroBlog\Filament\Resources\MetroResource\Pages\EditMetro;
use Fuelviews\SabHeroBlog\Filament\Resources\MetroResource\Pages\ListMetros;
use Fuelviews\SabHeroBlog\Filament\Resources\MetroResource\Pages\ViewMetro;
use Fuelviews\SabHeroBlog\Filament\Resources\MetroResource\RelationManagers\MetroRelationManager;
use Fuelviews\SabHeroBlog\Models\Metro;
use Illuminate\Support\Str;

class MetroResource extends Resource
{
    protected static ?string $model = Metro::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationGroup = 'Blog';

    protected static ?string $navigationLabel = 'Metros';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Metro::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Metro')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => Str::title($state)),

                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Parent Metro')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->colors([
                        'success' => 'state',
                        'info' => 'city',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('posts_count')
                    ->badge()
                    ->label('Posts Count')
                    ->counts('posts'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            Section::make('Metros')
                ->schema([
                    TextEntry::make('name')
                        ->label('Metro'),

                    TextEntry::make('parent.name')
                        ->label('Parent Metro')
                        ->visible(fn ($record) => $record->type === 'city'), // Only show for cities

                    TextEntry::make('type')
                        ->badge()
                        ->label('Type')
                        ->formatStateUsing(fn ($state) => ucfirst($state instanceof MetroType ? $state->value : $state)),

                ])->columns(2)
                ->icon('heroicon-o-square-3-stack-3d'),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            MetroRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMetros::route('/'),
            // 'edit' => EditMetro::route('/{record}/edit'),
            'view' => ViewMetro::route('/{record}'),
        ];
    }
}
