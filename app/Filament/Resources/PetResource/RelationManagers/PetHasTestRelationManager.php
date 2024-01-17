<?php

namespace App\Filament\Resources\PetResource\RelationManagers;

use App\Models\Person;
use App\Models\PetHasTest;
use App\Models\Test;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PetHasTestRelationManager extends RelationManager
{
    protected static string $relationship = 'pet_has_test';

    public static function getTitle($ownerRecord = null, $pageClass = null): string
    {
        return ucfirst(__('test'));
    }

    public static function getModelLabel(): string
    {
        return __('test');
    }
    public static function getPluralModelLabel(): string
    {
        return __('tests');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('test_id')
                    ->label(ucfirst(__('test')))
                    ->options(Test::all()->pluck('name', 'id'))
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->translateLabel()
                    ->native(false)
                    ->displayFormat(config('filament.date_format'))
                    ->required(),
                Forms\Components\Select::make('result')
                    ->translateLabel()
                    ->options([
                        'unknown' => __('Unkown'),
                        'positive' => __('Positive'),
                        'negative' => __('Negative'),
                    ])
                    ->default('unknown')
                    ->selectablePlaceholder(false)
                    ->required(),
                Forms\Components\TextInput::make('local')->translateLabel()->maxLength(50),
                Forms\Components\Select::make('person_id')->label(ucfirst(__('person')))->options(Person::getPersonByFlag(['veterinary', 'medication_volunteer'])->toArray())->searchable(),
                Forms\Components\Textarea::make('observation')->translateLabel()->maxLength(300)->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('file')
                    ->translateLabel()
                    ->disk('petsTests')
                    ->collection('pets-tests')
                    ->openable()
                    ->downloadable()
                    ->deletable(false)
                    ->hintAction(
                        Forms\Components\Actions\Action::make('removeFile')
                            ->label(__('Remove file'))
                            ->icon('heroicon-m-x-mark')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->action(function (Forms\Set $set, $state) {
                                $set('file', null);
                            })
                    )
                    ->columnSpan('full'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('test.name')
                    ->label(ucfirst(__('test')))
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->translateLabel()
                    ->sortable()
                    ->date(config('filament.date_format')),
                Tables\Columns\TextColumn::make('result')
                    ->translateLabel()
                    ->badge()
                    ->colors([
                        'warning' => 'unknown',
                        'danger' => 'positive',
                        'success' => 'negative',
                    ])
                    ->formatStateUsing(fn (string $state): string => __(ucfirst($state)))
                    ->sortable(),
                Tables\Columns\TextColumn::make('local')
                    ->translateLabel()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('person.name')
                    ->label(ucfirst(__('person')))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('observation')
                    ->translateLabel()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('person_id')
                    ->label(ucfirst(__('person')))
                    ->relationship('person', 'name')
                    ->searchable(),
                Tables\Filters\SelectFilter::make('result')
                    ->translateLabel()
                    ->options([
                        'unknown' => __('Unkown'),
                        'positive' => __('Positive'),
                        'negative' => __('Negative'),
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalHeading(__('filament-actions::create.single.modal.heading', ['label' => self::getTitle()])),
            ])
            ->actions([
                Tables\Actions\Action::make('file')
                    ->label(__('View file'))
                    ->color('info')
                    ->url(fn (PetHasTest $record) => $record->getMedia('pets-tests')[0]?->getFullUrl())
                    ->visible(fn (PetHasTest $record): bool => isset($record->getMedia('pets-tests')[0]) ? true : false)
                    ->openUrlInNewTab()
                    ->icon('tabler-file-download'),
                Tables\Actions\ViewAction::make()->modalHeading(fn ($record) => __('filament-actions::view.single.modal.heading', ['label' => $record->test()?->first()->name ?? self::getTitle()])),
                Tables\Actions\EditAction::make()->modalHeading(fn ($record) => __('filament-actions::edit.single.modal.heading', ['label' => $record->test()?->first()->name ?? self::getTitle()])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ])->defaultSort('date', 'desc');
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
