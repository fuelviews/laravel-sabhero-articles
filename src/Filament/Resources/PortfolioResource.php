<?php

namespace Fuelviews\SabHeroBlog\Filament\Resources;

use Fuelviews\SabHeroBlog\Enums\PortfolioType;
use Fuelviews\SabHeroBlog\Filament\Resources\PortfolioResource\Pages;
use Fuelviews\SabHeroBlog\Models\Portfolio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class PortfolioResource extends Resource
{
    protected static ?string $model = Portfolio::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Portfolio';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Portfolio::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('order')
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->sortable()
                    ->badge(),

                Tables\Columns\SpatieMediaLibraryImageColumn::make('before_image')
                    ->collection('before_image')
                    ->conversion('thumbnail')
                    ->label('Before')
                    ->width(100)
                    ->height(100),

                Tables\Columns\SpatieMediaLibraryImageColumn::make('after_image')
                    ->collection('after_image')
                    ->conversion('thumbnail')
                    ->label('After')
                    ->width(100)
                    ->height(100),

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
                Tables\Filters\SelectFilter::make('spacing')
                    ->options([
                        'yes' => 'Top and Bottom',
                        'no' => 'No Spacing',
                        'top' => 'Top Only',
                        'bottom' => 'Bottom Only',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->options(PortfolioType::class),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order');
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
            'index' => Pages\ListPortfolios::route('/'),
            'create' => Pages\CreatePortfolio::route('/create'),
            'edit' => Pages\EditPortfolio::route('/{record}/edit'),
        ];
    }
}
