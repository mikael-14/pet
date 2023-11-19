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

    protected static ?string $title = 'Test';

    protected static ?string $modelLabel  = 'test';

    protected static ?string $pluralModelLabel = 'tests';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('test_id')
                    ->options(Test::all()->pluck('name', 'id'))
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->native(false)
                    ->displayFormat(config('filament.date_format'))
                    ->required(),
                Forms\Components\Select::make('result')
                    ->options([
                        'unknown' => 'Unkown',
                        'positive' => 'Positive',
                        'negative' => 'Negative',
                    ])
                    ->default('unknown')
                    ->selectablePlaceholder(false)
                    ->required(),
                Forms\Components\TextInput::make('local')->maxLength(50),
                Forms\Components\Select::make('person_id')->options(Person::getPersonByFlag(['veterinary', 'medication_volunteer'])->toArray())->searchable(),
                Forms\Components\Textarea::make('observation')->maxLength(300)->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('file')
                    ->disk('petsTests')
                    ->collection('pets-tests')
                    ->openable()
                    ->downloadable()
                    ->deletable(false)
                    ->hintAction(
                        Forms\Components\Actions\Action::make('removeFile')
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->sortable()
                    ->date(config('filament.date_format')),
                Tables\Columns\TextColumn::make('result')
                    ->badge()
                    ->colors([
                        'warning' => 'unknown',
                        'danger' => 'positive',
                        'success' => 'negative',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('local')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('person.name')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('observation')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('person_id')
                    ->relationship('person', 'name')
                    ->searchable(),
                Tables\Filters\SelectFilter::make('result')
                    ->options([
                        'unknown' => 'Unkown',
                        'positive' => 'Positive',
                        'negative' => 'Negative',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalHeading(__('filament-support::actions/create.single.modal.heading', ['label' => self::$title])),
            ])
            ->actions([
                Tables\Actions\Action::make('file')
                    ->label('View file')
                    ->color('info')
                    ->url(fn (PetHasTest $record) => $record->getMedia('pets-tests')[0]?->getFullUrl())
                    ->visible(fn (PetHasTest $record): bool => isset($record->getMedia('pets-tests')[0]) ? true : false)
                    ->openUrlInNewTab()
                    ->icon('tabler-file-download'),
                Tables\Actions\ViewAction::make()->modalHeading(fn ($record) => __('filament-support::actions/view.single.modal.heading', ['label' => $record->test()?->first()->name ?? self::$title])),
                Tables\Actions\EditAction::make()->modalHeading(fn ($record) => __('filament-support::actions/edit.single.modal.heading', ['label' => $record->test()?->first()->name ?? self::$title])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
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
