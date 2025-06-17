<?php

namespace Fuelviews\SabHeroBlog\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Fuelviews\SabHeroBlog\Filament\Resources\UserResource;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Users'),
            'authors' => Tab::make('Authors')
                ->modifyQueryUsing(function ($query) {
                    $query->authors();
                })
                ->icon('heroicon-o-pencil-square'),
            'active_authors' => Tab::make('Active Authors')
                ->modifyQueryUsing(function ($query) {
                    $query->activeAuthors();
                })
                ->icon('heroicon-o-check-badge'),
        ];
    }
}