<?php

namespace Fuelviews\SabHeroBlog\Filament\Resources\PostResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Fuelviews\SabHeroBlog\Events\BlogPublished;
use Fuelviews\SabHeroBlog\Filament\Resources\PostResource;
use Illuminate\Contracts\Support\Htmlable;

class ViewPost extends ViewRecord
{
    protected static string $resource = PostResource::class;

    public function getTitle(): string|Htmlable
    {
        return $this->record->title;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sendNotification')
                ->label('Send Notification')
                ->requiresConfirmation()
                ->icon('heroicon-o-bell')
                ->action(function () {
                    event(new BlogPublished($this->record));
                })
                ->disabled(fn () => $this->record->isNotPublished()),

            Action::make('previewDefault')
                ->label('Preview')
                ->icon('heroicon-o-eye')
                ->url(fn () => route('sabhero-blog.post.show', $this->record->slug), true)
                ->disabled(fn () => $this->record->isNotPublished())
                ->visible(fn () => is_null($this->record->state) || is_null($this->record->city)),

            Action::make('previewWithLocation')
                ->label('Preview')
                ->icon('heroicon-o-eye')
                ->url(fn () => $this->generateLocationPreviewUrl(), true)
                ->disabled(fn () => $this->record->isNotPublished())
                ->visible(fn () => ! is_null($this->record->state) && ! is_null($this->record->city)),
        ];
    }

    protected function generateLocationPreviewUrl(): string
    {
        $stateSlug = $this->record->state?->slug ?? null;
        $citySlug = $this->record->city?->slug ?? null;
        $postSlug = $this->record->slug;

        return route('sabhero-blog.post.metro.show', [
            'state' => $stateSlug,
            'city' => $citySlug,
            'post' => $postSlug,
        ]);
    }
}
