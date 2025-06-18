<?php

namespace Fuelviews\SabHeroArticle\Filament\Resources\PostResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Fuelviews\SabHeroArticle\Enums\PostStatus;
use Fuelviews\SabHeroArticle\Filament\Resources\PostResource;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        if ($this->data['status'] === PostStatus::PUBLISHED->value) {
            $this->record->published_at = $this->record->published_at ?? date('Y-m-d H:i:s');
        }
    }
}
