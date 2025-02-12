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
        //$is_author = $this->confirm('Is author?', false);

        $userModel = config('auth.providers.users.model');

        /** @var Authenticatable $user */
        $user = new $userModel();
        $user->name = $name;
        $user->email = $email;
        $user->password = Hash::make($password);
        //$user->slug = Str::slug($name);
        //$user->is_author = $is_author;
        $user->save();

        $this->info("Filament user {$email} created successfully!");

        return static::SUCCESS;
    }
}
