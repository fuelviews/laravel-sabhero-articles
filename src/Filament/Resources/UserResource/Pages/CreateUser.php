<?php

namespace Fuelviews\SabHeroBlog\Filament\Resources\UserResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Fuelviews\SabHeroBlog\Filament\Resources\UserResource;
use Illuminate\Support\Facades\Hash;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generate a random password if none provided
        if (empty($data['password'])) {
            $data['password'] = Hash::make(str()->random(12));
        }

        return $data;
    }
}