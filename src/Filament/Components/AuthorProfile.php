<?php

namespace Fuelviews\SabHeroBlog\Filament\Components;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Jeffgreco13\FilamentBreezy\Livewire\MyProfileComponent;

class AuthorProfile extends MyProfileComponent
{
    protected string $view = 'sabhero-blog::filament.components.author-profile';

    public array $data;

    public function mount(): void
    {
        $this->data['bio'] = auth()->user()->bio;
        $this->data['links'] = auth()->user()->links;
    }

    public function submit(): void
    {
        auth()->user()->update([
            'bio' => $this->data['bio'],
            'links' => $this->data['links'],
        ]);

        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->send();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make('')
                    ->schema([
                        Forms\Components\Textarea::make('bio')
                                ->rows(5)
                            ->label('Bio'),
                        ])
                    ->label(''),

                Forms\Components\Section::make('Links')
                    ->schema([
                        Forms\Components\Grid::make(1)
                        ->schema([
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
                    ])
                    ->collapsed(),
            ])
            ->statePath('data');
    }
}
