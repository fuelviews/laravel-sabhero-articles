<?php

namespace Fuelviews\SabHeroBlog\Filament\Resources;

use Fuelviews\SabHeroBlog\Filament\Resources\UserResource\Pages;
use Fuelviews\SabHeroBlog\Filament\Tables\Columns\UserPhotoName;
use Fuelviews\SabHeroBlog\Models\User;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Account';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->live(true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set(
                                'slug',
                                Str::slug($state)
                            ))
                            ->required()
                            ->unique('users','id')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('slug')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('email')
                            ->required()
                            ->email()
                            ->columnSpan(2),

                        Forms\Components\Grid::make(2) // Define a 2-column grid
                        ->schema([
                            Forms\Components\Select::make('roles')
                                ->multiple()
                                ->preload()
                                ->relationship('roles', 'name'),

                            Forms\Components\Group::make([
                                Forms\Components\Toggle::make('is_author')
                                    ->label('Author')
                                    ->inline(false),
                            ])->extraAttributes(['class' => 'flex items-center justify-center h-full']),
                        ]),
                        SpatieMediaLibraryFileUpload::make('user.avatar')
                            ->label('Avatar')
                            ->collection('avatar')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
                Tables\Columns\ToggleColumn::make('is_author')
                    ->label('Author')
                    ->sortable()
                    ->onColor('primary')
                    ->offColor('gray')
                    ->afterStateUpdated(fn ($record, $state) => $record->update(['is_author' => $state])),
                Tables\Columns\SpatieMediaLibraryImageColumn::make('Avatar')
                    ->collection('avatar')
                    ->defaultImageUrl(url('https://ui-avatars.com/api/?background=gray&name='))
                    ->circular(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
