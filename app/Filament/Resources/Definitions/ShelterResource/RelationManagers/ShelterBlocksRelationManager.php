<?php

namespace App\Filament\Resources\Definitions\ShelterResource\RelationManagers;

use App\Filament\Resources\Definitions\ShelterResource\Pages\ViewShelter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShelterBlocksRelationManager extends RelationManager
{
    protected static string $relationship = 'shelter_blocks';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $title = 'Shelter Block';

    protected static ?string $modelLabel  = 'shelter block';

    protected static ?string $pluralModelLabel = 'shelter blocks';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(50),
                Forms\Components\ColorPicker::make('color'),
            ]);
    }

    public function table(Table $table): Table
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
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->modalHeading(__('filament-actions::create.single.modal.heading', ['label'  => self::$title]))
                ->visible(fn ($livewire) => $livewire->pageClass !== ViewShelter::class),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    } 
}
