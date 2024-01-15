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

    public static function getTitle($ownerRecord = null, $pageClass=null): string
    {
        return __('Shelter blocks');
    }

    public static function getModelLabel(): string
    {
        return __('shelter block');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                ->translateLabel()
                    ->required()
                    ->maxLength(50),
                Forms\Components\ColorPicker::make('color'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->translateLabel(),
                Tables\Columns\ColorColumn::make('color')->translateLabel(),
                Tables\Columns\TextColumn::make('created_at')->translateLabel()
                    ->dateTime(config('filament.date_time_format')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->modalHeading(__('filament-actions::create.single.modal.heading', ['label'  => self::getTitle()]))
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
