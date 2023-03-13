<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Closure;
use Filament\Facades\Filament;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use XliteDev\FilamentImpersonate\Pages\Actions\ImpersonateAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    use InteractsWithForms;
    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        return [
            ImpersonateAction::make()->record($this->getRecord()),
            Actions\DeleteAction::make(),
        ];
    }

    public function getFormSchema(): array
    {
        return [
            Section::make('Update your data')
                ->description('Update your account information')
                ->schema([
                    \Filament\Forms\Components\TextInput::make('name')
                        ->required(),
                    \Filament\Forms\Components\TextInput::make('email')
                        ->placeholder('email@example.com')
                        ->helperText('Make sure this email is valid and unique.')
                        ->required(),
                    \Filament\Forms\Components\Select::make('locale')->options(
                        [
                            'pt' => 'Portuguese',
                            'en' => 'English',
                        ]
                    )->default('pt')
                        ->disablePlaceholderSelection(),
                    \Filament\Forms\Components\Toggle::make('status')
                        ->inline(false)
                        ->helperText('Admin panel access')
                        ->disabled(!Filament::auth()->user()->isAdmin())
                        ->dehydrated(Filament::auth()->user()->isAdmin()),
                ])
                ->columns(2),
            Section::make('Change Password')
                ->description('Fill this in case you want to change password')
                ->schema([
                    \Filament\Forms\Components\TextInput::make('password')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state))
                        ->same('confirm_password')
                        ->visible(fn (Closure $get): bool => Filament::auth()->user()->email == $get('email')),
                    \Filament\Forms\Components\TextInput::make('confirm_password')
                        ->dehydrated(false)
                        ->password()
                        ->visible(fn (Closure $get): bool => Filament::auth()->user()->email == $get('email')),
                ])
                ->visible(fn (Closure $get): bool => Filament::auth()->user()->email == $get('email'))
                ->columns(2)
        ];
    }

    protected function afterSave(): void
    {
        
        $state = $this->form->getState();
        //change language
        if (in_array($state['locale'], config('filament-spatie-laravel-translatable-plugin.default_locales'))) {
            session()->put('locale', $state['locale']);
            app()->setLocale($state['locale']);
        }

        if (key_exists('password', $state)) {
            session()->forget('password_hash_' . config('filament.auth.guard'));
            Filament::auth()->login(Filament::auth()->user());
            $state['password']='';
            $state['confirm_password']='';
            $this->form->fill($state);
        }
    }
}
