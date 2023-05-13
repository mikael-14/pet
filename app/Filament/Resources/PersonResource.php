<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonResource\Pages;
use App\Filament\Resources\PersonResource\RelationManagers;
use App\Models\Person;
use App\Models\PersonFlag;
use App\Models\User;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PersonResource extends Resource implements HasShieldPermissions
{

    protected static ?string $model = Person::class;

    protected static ?string $navigationIcon = 'tabler-user';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

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
            'set_user',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(200),
                        Forms\Components\Select::make('gender')
                            ->options([
                                'undefined' => 'Undefined',
                                'male' => 'Male',
                                'female' => 'Female',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(table: Person::class, column: 'email', ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->required()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('vat')
                            ->maxLength(20)
                            ->unique(table: Person::class, column: 'vat',  ignoreRecord: true),
                        Forms\Components\TextInput::make('cc')
                            ->maxLength(30),
                        Forms\Components\TextInput::make('address')
                            ->maxLength(200),
                        Forms\Components\TextInput::make('town')
                            ->maxLength(200),
                        Forms\Components\DatePicker::make('birth_date')
                        ->displayFormat(config('filament.date_format')),
                        Forms\Components\Select::make('users_id')->options(
                            Person::avaibleUsers()
                        )->searchable()->visible(Filament::auth()->user()->can('set_user') ),
                        Forms\Components\Textarea::make('observation')
                            ->maxLength(65535),
                        Forms\Components\CheckboxList::make('flags')
                            ->options(PersonFlag::flags())
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('phone')->searchable(),
                Tables\Columns\TextColumn::make('vat')->searchable(),
                Tables\Columns\TextColumn::make('cc')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('birth_date')
                ->date(config('filament.date_format'))
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('address')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('town'),
                Tables\Columns\TextColumn::make('created_at')
                ->dateTime(config('filament.date_time_format'))->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('deleted_at')
                ->dateTime(config('filament.date_time_format'))->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListPeople::route('/'),
            'create' => Pages\CreatePerson::route('/create'),
            'edit' => Pages\EditPerson::route('/{record}/edit'),
            'view' => Pages\ViewPerson::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
