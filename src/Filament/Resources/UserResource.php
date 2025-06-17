<?php

namespace Fuelviews\SabHeroBlog\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
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
use Fuelviews\SabHeroBlog\Filament\Resources\UserResource\Pages\CreateUser;
use Fuelviews\SabHeroBlog\Filament\Resources\UserResource\Pages\EditUser;
use Fuelviews\SabHeroBlog\Filament\Resources\UserResource\Pages\ListUsers;
use Fuelviews\SabHeroBlog\Filament\Resources\UserResource\Pages\ViewUser;
use Fuelviews\SabHeroBlog\Filament\Tables\Columns\UserAvatar;
use Illuminate\Support\Str;

class UserResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getModel(): string
    {
        return config('sabhero-blog.user.model');
    }

    public static function getNavigationBadge(): ?string
    {
        $userModel = static::getModel();
        return (string) $userModel::authors()->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                if ($operation !== 'create') {
                                    return;
                                }
                                $set('slug', Str::slug($state));
                            }),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->rules(['alpha_dash']),

                        Forms\Components\Toggle::make('is_author')
                            ->label('Is Author')
                            ->helperText('Make this user visible as a blog author'),
                    ])->columns(2),

                Forms\Components\Section::make('Author Information')
                    ->schema([
                        Forms\Components\Textarea::make('bio')
                            ->maxLength(65535)
                            ->columnSpanFull(),

                        Forms\Components\Repeater::make('links')
                            ->schema([
                                Forms\Components\TextInput::make('label')
                                    ->required(),
                                Forms\Components\TextInput::make('url')
                                    ->url()
                                    ->required(),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Avatar')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('avatar')
                            ->responsiveImages()
                            ->image()
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
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\ImageColumn::make('avatar')
                    ->label('Avatar')
                    ->circular(),

                Tables\Columns\IconColumn::make('is_author')
                    ->boolean()
                    ->label('Author'),

                Tables\Columns\TextColumn::make('posts_count')
                    ->counts('posts')
                    ->label('Posts')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_author')
                    ->label('Authors only')
                    ->placeholder('All users')
                    ->trueLabel('Authors only')
                    ->falseLabel('Non-authors only'),
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
            Section::make('User Details')
                ->schema([
                    Fieldset::make('Basic Information')
                        ->schema([
                            TextEntry::make('name')
                                ->label('Name'),

                            TextEntry::make('email')
                                ->label('Email'),

                            TextEntry::make('slug')
                                ->label('Slug'),

                            TextEntry::make('is_author')
                                ->label('Is Author')
                                ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),
                        ]),

                    Fieldset::make('Author Information')
                        ->schema([
                            TextEntry::make('bio')
                                ->label('Bio')
                                ->columnSpanFull(),

                            TextEntry::make('links')
                                ->label('Links')
                                ->formatStateUsing(function ($state) {
                                    if (!$state || !is_array($state)) {
                                        return 'None';
                                    }
                                    return collect($state)
                                        ->map(fn ($link) => $link['label'] . ': ' . $link['url'])
                                        ->join(', ');
                                })
                                ->columnSpanFull(),
                        ]),

                    Fieldset::make('Statistics')
                        ->schema([
                            TextEntry::make('posts_count')
                                ->label('Total Posts')
                                ->getStateUsing(fn ($record) => $record->posts()->count()),

                            TextEntry::make('published_posts_count')
                                ->label('Published Posts')
                                ->getStateUsing(fn ($record) => $record->posts()->published()->count()),

                            TextEntry::make('created_at')
                                ->label('Joined')
                                ->dateTime(),

                            TextEntry::make('updated_at')
                                ->label('Last Updated')
                                ->dateTime(),
                        ]),

                    Fieldset::make('Avatar')
                        ->schema([
                            SpatieMediaLibraryImageEntry::make('avatar')
                                ->collection('avatar')
                                ->label('Avatar Image'),
                        ]),
                ]),
        ]);
    }

    public static function getRecordTitle($record): string
    {
        return $record->name;
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewUser::class,
            EditUser::class,
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
