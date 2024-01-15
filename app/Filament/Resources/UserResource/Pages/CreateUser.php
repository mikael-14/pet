<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\ModelHasRole;
use App\Models\Role;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Form;
class CreateUser extends CreateRecord 
{
    
    protected static string $resource = UserResource::class;

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            \Filament\Forms\Components\Section::make()
                ->schema([
                    \Filament\Forms\Components\TextInput::make('name')
                    ->label(__('Username'))
                        ->required(),
                    \Filament\Forms\Components\TextInput::make('email')
                    ->translateLabel()
                        ->placeholder('email@example.com')
                        ->helperText(__('Make sure this email is valid and unique'))
                        ->unique(table: User::class, column: 'email', ignoreRecord: true)
                        ->required(),
                    \Filament\Forms\Components\Select::make('locale')->options(
                        config('filament-spatie-laravel-translatable-plugin.available_locales')
                    )->default('pt')
                        ->selectablePlaceholder(false),
                    \Filament\Forms\Components\Toggle::make('status')
                        ->inline(false)
                        ->helperText(__('Admin panel access'))
                        ->default(1),
                    \Filament\Forms\Components\Select::make('role')
                    ->translateLabel()
                        ->options(
                            Role::all()->pluck('name', 'id')
                                ->toArray()
                        ),
                ])->columns(2),
            \Filament\Forms\Components\Section::make()
                ->schema([
                    \Filament\Forms\Components\TextInput::make('password')
                        ->label(__('Define a password'))
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state))
                        ->same('confirm_password')
                        ->required(),
                    \Filament\Forms\Components\TextInput::make('confirm_password')
                        ->label(__('Confirm password'))
                        ->password()
                        ->dehydrated(false)
                        ->required(),
                ])
                ->columns(2),
        ]);
    }
    protected function afterCreate(): void
    {
        // Runs after the form fields are saved to the database.
        //insert role in the table
        $state = $this->form->getState();
        if (isset($state['role'][0])) {
            ModelHasRole::insert([
                'role_id' => $state['role'][0],
                'model_type' => 'App\Models\User',
                'model_id' => $this->record->id
            ]);
        }
    }
}
