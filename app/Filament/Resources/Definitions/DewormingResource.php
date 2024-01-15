<?php

namespace App\Filament\Resources\Definitions;

use App\Filament\Resources\Definitions\DewormingResource\Pages;
use App\Filament\Resources\Definitions\DewormingResource\RelationManagers;
use App\Models\Deworming;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DewormingResource extends Resource
{
    protected static ?string $model = Deworming::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $slug = 'definitions/deworming';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Definitions';

    public static function getNavigationLabel(): string
    {
        return __('dewormings');
    }
    public static function getModelLabel(): string
    {
        return __('deworming');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->translateLabel()
                    ->required()
                    ->maxLength(100),
                Forms\Components\Select::make('type')
                    ->translateLabel()
                    ->options(__('pet/deworming'))
                    ->required(),
                Forms\Components\TextInput::make('expire')
                    ->translateLabel()
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required()
                    ->suffix(__('days'))
                    ->helperText(__('Number of days to be renewed. Leave 0 (zero) if don\'t need to be renewed')),
                Forms\Components\TextInput::make('notification')
                    ->translateLabel()
                    ->numeric()
                    ->suffix(__('days'))
                    ->helperText(__('Number of day before/after to send notification to be rescheduled')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('type')
                    ->translateLabel()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'internal' => __('Internal'),
                        'external' => __('External'),
                        'internal and external' => 'Internal and external',
                    })->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('expire')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('notification')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('created_at')
                    ->translateLabel()
                    ->dateTime(config('filament.date_time_format')),
                Tables\Columns\TextColumn::make('updated_at')
                    ->translateLabel()
                    ->dateTime(config('filament.date_time_format')),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->translateLabel()
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
            'index' => Pages\ManageDewormings::route('/'),
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
