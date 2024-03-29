<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\ModelHasRole;
use App\Models\Role;
use App\Models\User;
use Closure;
use Filament\Facades\Filament;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use XliteDev\FilamentImpersonate\Pages\Actions\ImpersonateAction;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{

    protected static string $resource = UserResource::class;

    public function mutateFormDataBeforeFill(array $data): array
    {
        $data['role'] = ModelHasRole::where('model_id', $data['id'])->pluck('role_id')->toArray();
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            ImpersonateAction::make()->record($this->getRecord()),
            Actions\DeleteAction::make(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->heading(__('User data'))
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('name')
                            ->label(__('Username'))
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('email')
                            ->translateLabel()
                            ->placeholder('email@example.com')
                            ->helperText(__('Make sure this email is valid and unique'))
                            ->required()
                            ->unique(table: User::class, column: 'email', ignorable: fn () => $this->getRecord(), ignoreRecord: true),
                        \Filament\Forms\Components\Select::make('locale')->options(
                            config('filament-spatie-laravel-translatable-plugin.available_locales')
                        )->default('pt')
                            ->translateLabel()
                            ->selectablePlaceholder(false),
                        \Filament\Forms\Components\Toggle::make('status')
                            ->label(__('Active'))
                            ->inline(false)
                            ->helperText(__('Admin panel access'))
                            ->disabled(!Filament::auth()->user()->isAdmin())
                            ->dehydrated(Filament::auth()->user()->isAdmin()),
                        \Filament\Forms\Components\Select::make('role')
                            ->translateLabel()
                            ->options(
                                Role::all()->pluck('name', 'id')
                                    ->toArray()
                            )->disabled(!Filament::auth()->user()->isAdmin())
                            ->dehydrated(Filament::auth()->user()->isAdmin()),
                    ])
                    ->columns(2),
                Section::make()
                    ->heading(__('Change password'))
                    ->description(__('Fill this in case you want to change password'))
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('password')
                            ->translateLabel()
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->same('confirm_password')
                            ->visible(fn (\Filament\Forms\Get $get): bool => Filament::auth()->user()->id == $this->record->id),
                        \Filament\Forms\Components\TextInput::make('confirm_password')
                            ->translateLabel()
                            ->dehydrated(false)
                            ->password()
                            ->visible(fn (\Filament\Forms\Get $get): bool => Filament::auth()->user()->id == $this->record->id),
                    ])
                    ->visible(fn (\Filament\Forms\Get $get): bool => Filament::auth()->user()->id == $this->record->id)
                    ->columns(2)
            ]);
    }
    // protected function getRedirectUrl(): string
    // {
    //     //don't know how to fix this (livewire component) field refresh 
    //     //for now let's refresh the page 
    //     return request()->header('Referer');
    // }
    protected function afterSave(): void
    {
        $state = $this->form->getState();
        //change language
        if (!empty($state['locale']) && Filament::auth()->user()->id == $this->record->id && array_key_exists($state['locale'], config('filament-spatie-laravel-translatable-plugin.available_locales'))) {
            session()->put('locale', $state['locale']);
            app()->setLocale($state['locale']);
        }
        //change password
        if (key_exists('password', $state)) {
            session()->forget('password_hash_' . config('filament.auth.guard'));
            Filament::auth()->login(Filament::auth()->user());
            $state['password'] = '';
            $state['confirm_password'] = '';
        }
        //change role
        ModelHasRole::where('model_id', $this->record->id)->delete();
        if (isset($state['role'][0])) {
            ModelHasRole::insert([
                'role_id' => $state['role'][0],
                'model_type' => 'App\Models\User',
                'model_id' => $this->record->id
            ]);
        }

        $this->form->fill($state);
    }
}
