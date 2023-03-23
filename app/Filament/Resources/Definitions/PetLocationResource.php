<?php

namespace App\Filament\Resources\Definitions;

use App\Filament\Resources\Definitions\PetLocationResource\Pages;
use App\Filament\Resources\Definitions\PetLocationResource\RelationManagers;
use App\Models\PetLocation;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PetLocationResource extends Resource
{
    protected static ?string $model = PetLocation::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $slug = 'definitions/pet-location';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Definitions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(20),
                Forms\Components\ColorPicker::make('color'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\ColorColumn::make('color'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(config('filament.date_time_format')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
            
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePetLocations::route('/'),
        ];
    }    
}
