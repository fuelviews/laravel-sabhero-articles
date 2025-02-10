<?php

namespace Fuelviews\SabHeroBlog\Filament\Resources;

use Althinect\FilamentSpatieRolesPermissions\Resources\RoleResource as BaseRoleResource;
use Illuminate\Support\Facades\Auth;

class RoleResource extends BaseRoleResource
{
    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        if ($user === null || ! Auth::check()) {
            return false;
        }

        return self::userHasRole($user);
    }

    private static function userHasRole($user): bool
    {
        return true /*$user->getRoleNames()
            ->map(fn ($name) => strtolower($name))
            ->contains(strtolower('super admin'))*/;
    }
}
