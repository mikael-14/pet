<?php

namespace App\Filament\Resources\PetResource\RelationManagers;

use App\Models\Diet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;

class PetHasDietRelationManager extends RelationManager
{
    protected static string $relationship = 'pet_has_diet';

    public static function getTitle($ownerRecord = null, $pageClass = null): string
    {
        return ucfirst(__('diet'));
    }

    public static function getModelLabel(): ?string
    {
        return __('diet');
    }
    public static function getPluralModelLabel(): string
    {
        return __('diets');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('diet_id')
                    ->options(Diet::all()->pluck('name', 'id'))
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\DatePicker::make('date')
                ->native(false)
                    ->displayFormat(config('filament.date_format'))
                    ->required(),
                Forms\Components\TextInput::make('portion'),
                Forms\Components\Textarea::make('observation')->maxLength(300)->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('diet.name')->searchable(),
                Tables\Columns\TextColumn::make('date')
                ->sortable()
                ->date(config('filament.date_format')),
                Tables\Columns\TextColumn::make('portion'),
                Tables\Columns\TextColumn::make('observation')
                ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->modalHeading( __('filament-actions::create.single.modal.heading', ['label' => self::getTitle()])),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->modalHeading(fn ($record) => __('filament-actions::view.single.modal.heading', ['label' => $record->diet()?->first()->name ?? self::getTitle()])),
                Tables\Actions\EditAction::make()->modalHeading(fn ($record) => __('filament-actions::edit.single.modal.heading', ['label' => $record->diet()?->first()->name ?? self::getTitle()])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->groupedBulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])->defaultSort('date', 'desc');
    }
   
}
