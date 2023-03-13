<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
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
                        [
                            'pt' => 'Portuguese',
                            'en' => 'English',
                        ]
                    )->default('pt')
                        ->disablePlaceholderSelection(),
                    \Filament\Forms\Components\Toggle::make('status')
                        ->inline(false)
                        ->helperText('Admin panel access')
                        ->default(1),
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
}
