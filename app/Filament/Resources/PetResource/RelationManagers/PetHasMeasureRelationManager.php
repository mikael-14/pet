<?php

namespace App\Filament\Resources\PetResource\RelationManagers;

use App\Models\Person;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Contracts\HasRelationshipTable;

class PetHasMeasureRelationManager extends RelationManager
{
    protected static string $relationship = 'pet_has_measure';

    protected static ?string $title = 'Measure';

    protected static ?string $recordTitleAttribute = 'type';

    protected static ?string $modelLabel  = 'measure';

    protected static ?string $pluralModelLabel = 'measures';

    public function form(Form $form): Form
    {
        $configs = __('pet/measures');
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
                    ->mask('9'
    //                     RawJs::make(<<<'JS'
    //      '999999999999.999'
    // JS)
                     
    //                         ->decimalPlaces(3) // Set the number of digits after the decimal point.
    //                         ->decimalSeparator('.') // Add a separator for decimal numbers.
    //                         ->mapToDecimalSeparator([',']) // Map additional characters to the decimal separator.
    //                         ->minValue(0) // Set the minimum value that the number can be.
    //                         ->padFractionalZeros() // Pad zeros at the end of the number to always maintain the maximum number of decimal places.
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
            Forms\Components\DatePicker::make('date')->native(false)->displayFormat(config('filament.date_format'))->required(),
            Forms\Components\TextInput::make('local')->maxLength(50),
            Forms\Components\Select::make('person_id')->options(Person::getPersonByFlag(['veterinary','medication_volunteer'])->toArray())->searchable()->columnSpanFull(),
            Forms\Components\Textarea::make('observation')->maxLength(300)->columnSpanFull(),
        ]);
        return $form
            ->schema($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')->getStateUsing(fn ($record) => $record->getConfigMeasureName())
                    ->searchable(),
                Tables\Columns\ViewColumn::make('value')->view('filament.tables.columns.label-variation'),
                Tables\Columns\TextColumn::make('date')
                    ->sortable()
                    ->date(config('filament.date_format')),
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
                ->searchable()
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->afterFormValidated(function (CreateAction $action, array $data) {
                    // Runs after the form fields are validated when the form is submitted.
                    $filteredArr = array_filter(
                        $data,
                        fn ($key) => str_starts_with($key, 'value-'),
                        ARRAY_FILTER_USE_KEY
                    );
                    $filteredArr = array_filter($filteredArr);
                    if (count($filteredArr) === 0)
                        $action->halt();
                })->using(function (HasRelationshipTable $livewire, array $data) {
                    $filteredArr = array_filter(
                        $data,
                        fn ($key) => str_starts_with($key, 'type-'),
                        ARRAY_FILTER_USE_KEY
                    );
                    $newdata = [];
                    foreach ($filteredArr as $type) {
                        if ($data['value-' . $type]) {
                            $data['value'] = $data['value-' . $type];
                            $data['type'] = $type;
                            $newdata[] = $data;
                        }
                    }
                    array_pop($newdata);
                    foreach ($newdata as $row) {
                        $livewire->getRelationship()->create($row);
                    }
                    return $livewire->getRelationship()->create($data);
                })->modalHeading(__('filament-support::actions/create.single.modal.heading', ['label' => self::$title])),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->modalHeading(fn ($record) => __('filament-support::actions/view.single.modal.heading', ['label' => $record?->getConfigMeasureName() ?? self::$title])),
                Tables\Actions\EditAction::make()->modalHeading(fn ($record) => __('filament-support::actions/edit.single.modal.heading', ['label' => $record?->getConfigMeasureName() ?? self::$title])),
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
