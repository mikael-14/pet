<?php

namespace App\Filament\Resources\Definitions;

use App\Filament\Resources\Definitions\VaccineResource\Pages;
use App\Filament\Resources\Definitions\VaccineResource\RelationManagers;
use App\Models\Vaccine;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VaccineResource extends Resource
{
    protected static ?string $model = Vaccine::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $slug = 'definitions/vaccine';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Definitions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(100)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('expire')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required()
                    ->suffix('days')
                    ->helperText('Number of days to be renewed. Leave 0 (zero) if don\'t need to be renewed'),
                Forms\Components\TextInput::make('notification')
                    ->numeric()
                    ->suffix('days')
                    ->helperText('Number of day before/after to lauch notification to be rescheduled'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('expire'),
                Tables\Columns\TextColumn::make('notification'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(config('filament.date_time_format'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime(config('filament.date_time_format'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageVaccines::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
