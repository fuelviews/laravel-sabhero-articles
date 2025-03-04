<?php

namespace Fuelviews\SabHeroBlog\Filament\Resources\PostResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Fuelviews\SabHeroBlog\Filament\Resources\PostResource;
use Fuelviews\SabHeroBlog\Filament\Resources\PostResource\Widgets\BlogPostPublishedChart;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            BlogPostPublishedChart::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'published' => Tab::make('Published')
                ->modifyQueryUsing(function ($query) {
                    $query->published();
                })->icon('heroicon-o-check-badge'),
            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(function ($query) {
                    $query->pending();
                })
                ->icon('heroicon-o-clock'),
            'scheduled' => Tab::make('Scheduled')
                ->modifyQueryUsing(function ($query) {
                    $query->scheduled();
                })
                ->icon('heroicon-o-calendar-days'),
        ];
    }
}
