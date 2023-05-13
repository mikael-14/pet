<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use BezhanSalleh\FilamentShield\FilamentShield;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use XliteDev\FilamentImpersonate\Tables\Actions\ImpersonateAction;

class UserResource extends Resource implements HasShieldPermissions
{

    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'Filament Shield';

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'view_owned',
            'view_all',
            'access_lock_resource',
        ];
    }

 
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\IconColumn::make('status')
                    ->options([
                        'heroicon-o-x-circle',
                        'heroicon-o-check-circle' => 1,
                    ])->colors([
                        'danger',
                        'success' => 1,
                    ]),
                Tables\Columns\TextColumn::make('locale'),
                Tables\Columns\TextColumn::make('created_at'),
            ])
            ->filters([
                //
            ])
            ->actions([
                ImpersonateAction::make()->label('Impersonate'),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        if(Filament::auth()->user()->can('view_owned_user') && !Filament::auth()->user()->can('view_all_user') ){
            return parent::getEloquentQuery()->where(['id'=>auth()->user()->id]);
        }
        return parent::getEloquentQuery();
    }
}
