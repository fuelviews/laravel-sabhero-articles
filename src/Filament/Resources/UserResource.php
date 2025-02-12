<?php

namespace Fuelviews\SabHeroBlog\Filament\Resources;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Fuelviews\SabHeroBlog\Filament\Resources\UserResource\Pages\CreateUser;
use Fuelviews\SabHeroBlog\Filament\Resources\UserResource\Pages\EditUser;
use Fuelviews\SabHeroBlog\Filament\Resources\UserResource\Pages\ListUsers;
use Fuelviews\SabHeroBlog\Filament\Resources\UserResource\Pages\ViewUser;
use Fuelviews\SabHeroBlog\Filament\Tables\Columns\UserAvatar;
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
                            ->columnSpan(1),

                        Forms\Components\Group::make([
                            Forms\Components\Toggle::make('is_author')
                                ->label('Author')
                                ->inline(false),
                        ])->extraAttributes(['class' => 'flex items-center justify-center h-full']),


                        Forms\Components\SpatieMediaLibraryFileUpload::make('user.avatar')
                            ->responsiveImages()
                            ->label('Avatar')
                            ->collection('avatar')
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(1)
                            ->schema([
                                Forms\Components\Textarea::make('bio')
                                    ->rows(5)
                                    ->label('Bio')
                                    ->columnSpan(1),

                                Forms\Components\Repeater::make('links')
                                    ->schema([
                                        Forms\Components\Select::make('site')
                                            ->label('Site')
                                            ->options([
                                                'x' => 'X',
                                                'facebook' => 'Facebook',
                                                'linkedin' => 'Linkedin',
                                                'youtube' => 'Youtube',
                                                'github' => 'Github',
                                                'instagram' => 'Instagram',
                                                'threads' => 'Threads',
                                                'personal' => 'Personal',
                                                'business' => 'Business',
                                            ])
                                            ->required()
                                            ->columnSpanFull(),

                                        Forms\Components\TextInput::make('link')
                                            ->label('Link')
                                            ->url()
                                            ->required(),
                                    ])
                                    ->label('')
                                    ->addActionLabel('Add your links'),
                            ]),
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
            //'edit' => EditUser::route('/{record}/edit'),
            'view' => ViewUser::route('/{record}'),
        ];
    }
}
