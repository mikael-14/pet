<?php

namespace App\Filament\Resources\PetResource\RelationManagers;

use App\Models\Diet;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PetHasDietRelationManager extends RelationManager
{
    protected static string $relationship = 'pet_has_diet';

    protected static ?string $title = 'Diet';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('diets_id')
                    ->options(Diet::all()->pluck('name', 'id'))
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->displayFormat(config('filament.date_format'))
                    ->required(),
                Forms\Components\TextInput::make('portion'),
                Forms\Components\Textarea::make('observation')->maxLength(300)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
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
                Tables\Actions\CreateAction::make()->modalHeading( __('filament-support::actions/create.single.modal.heading', ['label' => self::getTitle()])),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->modalHeading(fn ($record) => __('filament-support::actions/view.single.modal.heading', ['label' => $record->diet()?->first()->name ?? self::getTitle()])),
                Tables\Actions\EditAction::make()->modalHeading(fn ($record) => __('filament-support::actions/edit.single.modal.heading', ['label' => $record->diet()?->first()->name ?? self::getTitle()])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    protected function getDefaultTableSortColumn(): ?string
    {
        return 'date';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    } 
}