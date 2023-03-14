<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\ModelHasRole;
use App\Models\Role;
use App\Models\User;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Support\Facades\Hash;

class CreateUser extends CreateRecord
{
    use InteractsWithForms;

    protected static string $resource = UserResource::class;

    public function getFormSchema(): array
    {
        return [
            \Filament\Forms\Components\Card::make()
                ->schema([
                    \Filament\Forms\Components\TextInput::make('name')
                        ->required(),
                    \Filament\Forms\Components\TextInput::make('email')
                        ->placeholder('email@example.com')
                        ->helperText('Make sure this email is valid and unique.')
                        ->unique(table: User::class,column: 'email', ignoreRecord: true)
                        ->required(),
                    \Filament\Forms\Components\Select::make('locale')->options(
                        config('filament-spatie-laravel-translatable-plugin.available_locales')
                    )->default('pt')
                        ->disablePlaceholderSelection(),
                    \Filament\Forms\Components\Toggle::make('status')
                        ->inline(false)
                        ->helperText('Admin panel access')
                        ->default(1),
                    \Filament\Forms\Components\Select::make('roles')
                        ->multiple()
                        ->options(
                            Role::all()->pluck('name', 'id')
                                ->toArray()
                        ),
                ])->columns(2),
                \Filament\Forms\Components\Card::make()
                ->schema([
                    \Filament\Forms\Components\TextInput::make('password')
                        ->label('Define a password')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state))
                        ->same('confirm_password')
                        ->required(),
                    \Filament\Forms\Components\TextInput::make('confirm_password')
                        ->label('Confirm password')
                        ->password()
                        ->dehydrated(false)
                        ->required(),
                ])
                ->columns(2),
        ];
    }
    protected function afterCreate(): void
    {
        // Runs after the form fields are saved to the database.
        //insert roles in the table
        $state = $this->form->getState();
        $id = $this->record->id;
        if (!empty($state['roles'])) {
            $new_data= array();
            foreach($state['roles'] as $value){
                $new_data[]= [
                    'role_id' => $value,
                    'model_type' => 'App\Models\User',
                    'model_id' => $this->record->id
                ];
            }
            ModelHasRole::insert($new_data);
        }
    }
}
