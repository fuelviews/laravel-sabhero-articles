<?php

namespace Fuelviews\SabHeroBlog\Commands;

use Filament\Commands\MakeUserCommand;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MakeFilamentUserCommand extends MakeUserCommand
{
    protected $signature = 'make:filament-user';

    public function handle(): int
    {
        $name = $this->ask('Name');
        $email = $this->ask('Email address');
        $password = $this->secret('Password');
        $author = $this->confirm('Is this user a Author?', false);

        $userModel = config('auth.providers.users.model');

        /** @var Authenticatable $user */
        $user = new $userModel;
        $user->name = ucwords($name);
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->is_author = $author;
        $user->save();

        $this->info("Filament user {$email} created successfully!");

        return static::SUCCESS;
    }
}
