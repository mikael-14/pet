<?php

namespace App\Filament\Resources\Definitions;

use App\Filament\Resources\Definitions\DewormingResource\Pages;
use App\Filament\Resources\Definitions\DewormingResource\RelationManagers;
use App\Models\Deworming;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DewormingResource extends Resource
{
    protected static ?string $model = Deworming::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $slug = 'definitions/deworming';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Definitions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(100),
                Forms\Components\Select::make('type')
                    ->options([
                        'internal' => 'Internal',
                        'external' => 'External',
                        'internal and external' => 'Internal and external',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('expires')
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
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('expires'),
                Tables\Columns\TextColumn::make('notification'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->toggledHiddenByDefault(),
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
