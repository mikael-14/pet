<?php

namespace App\Filament\Resources\PetResource\RelationManagers;

use App\Models\Test;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PetHasTestRelationManager extends RelationManager
{
    protected static string $relationship = 'pet_has_test';

    protected static ?string $title = 'Test';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                  Forms\Components\Select::make('tests_id')
                    ->options(Test::all()->pluck('name', 'id'))
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->displayFormat(config('filament.date_format'))
                    ->required(),
                Forms\Components\Select::make('result')
                    ->options([
                        'unknown' => 'Unkown',
                        'positive' => 'Positive',
                        'negative' => 'Negative',
                    ])
                        ->disablePlaceholderSelection()
                    ->required(),
                Forms\Components\TextInput::make('local')->maxLength(50),
                Forms\Components\TextInput::make('application')->maxLength(100),
                Forms\Components\Textarea::make('observation')->maxLength(300)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('test.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->sortable()
                    ->date(config('filament.date_format')),
                Tables\Columns\BadgeColumn::make('result')
                    ->colors([
                        'warning' => 'unknown',
                        'success' => 'positive',
                        'danger' => 'negative',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('local')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('application')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('observation')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('result')
                ->options([
                    'unknown' => 'Unkown',
                    'positive' => 'Positive',
                    'negative' => 'Negative',
                ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->modalHeading( __('filament-support::actions/create.single.modal.heading', ['label' => self::getTitle()])),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->modalHeading(fn ($record) => __('filament-support::actions/view.single.modal.heading', ['label' => $record->test()?->first()->name ?? self::getTitle()])),
                Tables\Actions\EditAction::make()->modalHeading(fn ($record) => __('filament-support::actions/edit.single.modal.heading', ['label' => $record->test()?->first()->name ?? self::getTitle()])),
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
