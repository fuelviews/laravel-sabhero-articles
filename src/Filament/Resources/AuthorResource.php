<?php

namespace Fuelviews\SabHeroBlog\Filament\Resources;

use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Fuelviews\SabHeroBlog\Models\Author;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Fuelviews\SabHeroBlog\Filament\Resources\AuthorResource\Pages\CreateUser;
use Fuelviews\SabHeroBlog\Filament\Resources\AuthorResource\Pages\EditUser;
use Fuelviews\SabHeroBlog\Filament\Resources\AuthorResource\Pages\ListUsers;
use Fuelviews\SabHeroBlog\Filament\Resources\AuthorResource\Pages\ViewUser;
use Fuelviews\SabHeroBlog\Filament\Tables\Columns\UserAvatar;
use Illuminate\Support\Str;

class AuthorResource extends Resource
{
    protected static ?string $model = Author::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Account';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Author::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->searchable()->sortable(),
                Tables\Columns\ToggleColumn::make('is_author')
                    ->label('Author')
                    ->sortable()
                    ->onColor('primary')
                    ->offColor('gray')
                    ->afterStateUpdated(fn ($record, $state) => $record->update(['is_author' => $state])),
                UserAvatar::make('user')
                    ->label('Avatar')
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
            Section::make('User Info')
                ->schema([
                    TextEntry::make('user.name')
                        ->label('Name'),

                    TextEntry::make('user.email')
                        ->label('Email'),

                ])->columns(),

            Section::make('Author Info')
                ->schema([
                    TextEntry::make('is_author')
                        ->label('Is Author')
                        ->formatStateUsing(function ($state) {
                            return $state ? 'Yes' : 'No';
                        }),

                    TextEntry::make('slug')
                        ->label('Slug'),

                    TextEntry::make('bio')
                        ->label('Bio'),

                    SpatieMediaLibraryImageEntry::make('avatar')
                        ->collection('avatar')
                        ->label('Avatar')
                        ->circular(),
                ])->columns(3),
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
            'view' => ViewUser::route('/{record}'),
        ];
    }
}
