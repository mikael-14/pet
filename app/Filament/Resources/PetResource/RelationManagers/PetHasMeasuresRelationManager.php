<?php

namespace App\Filament\Resources\PetResource\RelationManagers;

use App\Models\PetHasMeasure;
use App\Models\PetsHasMeasure;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Contracts\HasRelationshipTable;

class PetHasMeasuresRelationManager extends RelationManager
{
    protected static string $relationship = 'pet_has_measures';

    protected static ?string $title = 'Measure';

    protected static ?string $recordTitleAttribute = 'type';

    public static function form(Form $form): Form
    {
        $configs = config('pet-measures', []);
        $schema = [];
        foreach ($configs as $key => $config) {
            $schema = array_merge($schema, [
                Forms\Components\Hidden::make('type-' . $key)->default($key)->visibleOn('create'),
                Forms\Components\TextInput::make('name-type-' . $key)
                    ->label('Type')
                    ->default($config['name'])->visibleOn('create')
                    ->dehydrated(false)
                    ->disabled(),
                Forms\Components\TextInput::make('value-' . $key)
                    ->label('Value')
                    ->visibleOn('create')
                    ->numeric()
                    ->mask(
                        fn (Forms\Components\TextInput\Mask $mask) => $mask
                            ->numeric()
                            ->decimalPlaces(3) // Set the number of digits after the decimal point.
                            ->decimalSeparator('.') // Add a separator for decimal numbers.
                            ->mapToDecimalSeparator([',']) // Map additional characters to the decimal separator.
                            ->minValue(0) // Set the minimum value that the number can be.
                            ->padFractionalZeros() // Pad zeros at the end of the number to always maintain the maximum number of decimal places.
                    ),
            ]);
        }
        $schema = array_merge($schema, [
            Forms\Components\Hidden::make('type')->visibleOn('edit'),
            Forms\Components\TextInput::make('name-type')
                ->formatStateUsing(fn ($record) => $record?->getConfigMeasureName() ?? '')
                ->visibleOn('edit')
                ->dehydrated(false)
                ->disabled(),
            Forms\Components\TextInput::make('value')
                ->numeric()
                ->visibleOn('edit')
                ->mask(
                    fn (Forms\Components\TextInput\Mask $mask) => $mask
                        ->numeric()
                        ->decimalPlaces(3) // Set the number of digits after the decimal point.
                        ->decimalSeparator('.') // Add a separator for decimal numbers.
                        ->mapToDecimalSeparator([',']) // Map additional characters to the decimal separator.
                        ->minValue(0) // Set the minimum value that the number can be.
                        ->padFractionalZeros() // Pad zeros at the end of the number to always maintain the maximum number of decimal places.
                ),
            Forms\Components\DatePicker::make('date')->displayFormat(config('filament.date_format'))->required(),
            Forms\Components\TextInput::make('local')->maxLength(50),
            Forms\Components\TextInput::make('application')->maxLength(100),
            Forms\Components\Textarea::make('observation')->maxLength(300)->columnSpanFull(),
        ]);
        return $form
            ->schema($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')->getStateUsing(fn ($record) => $record->getConfigMeasureName())
                    ->searchable(),
                Tables\Columns\ViewColumn::make('value')->view('filament.tables.columns.label-variation'),
                Tables\Columns\TextColumn::make('date')
                    ->sortable()
                    ->date(config('filament.date_format')),
                Tables\Columns\TextColumn::make('expires_at')
                    ->sortable()
                    ->date(config('filament.date_format')),
                Tables\Columns\TextColumn::make('local')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('application')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('observation')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->afterFormValidated(function (CreateAction $action, array $data) {
                    $filteredArr = array_filter(
                        $data,
                        fn ($key) => str_starts_with($key, 'value-'),
                        ARRAY_FILTER_USE_KEY
                    );
                    $filteredArr = array_filter($filteredArr);
                    if (count($filteredArr) === 0)
                        $action->halt();
                    // Runs before the form fields are validated when the form is submitted.
                })->using(function (HasRelationshipTable $livewire, array $data) {
                    $filteredArr = array_filter(
                        $data,
                        fn ($key) => str_starts_with($key, 'type-'),
                        ARRAY_FILTER_USE_KEY
                    );
                    $newdata = [];
                    foreach ($filteredArr as $type) {
                        if($data['value-'.$type]) {
                            $data['value'] = $data['value-'.$type];
                            $data['type'] = $type;
                            $newdata[]=$data;
                        }
                    }
                    array_pop($newdata);
                    foreach($newdata as $row) {
                        $livewire->getRelationship()->create($row);
                    }
                    return $livewire->getRelationship()->create($data);
                })->modalHeading(__('filament-support::actions/create.single.modal.heading', ['label' => self::getTitle()])),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->modalHeading(fn ($record) => __('filament-support::actions/view.single.modal.heading', ['label' => $record?->getConfigMeasureName() ?? self::getTitle()])),
                Tables\Actions\EditAction::make()->modalHeading(fn ($record) => __('filament-support::actions/edit.single.modal.heading', ['label' => $record?->getConfigMeasureName() ?? self::getTitle()])),
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