<?php

namespace App\Filament\Resources\PetResource\RelationManagers;

use App\Enums\PetMeasure;
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
use Filament\Support\RawJs;

class PetHasMeasureRelationManager extends RelationManager
{
    protected static string $relationship = 'pet_has_measure';

    protected static ?string $recordTitleAttribute = 'type';

    public static function getTitle($ownerRecord = null, $pageClass = null): string
    {
        return ucfirst(__('measure'));
    }

    public static function getModelLabel(): string
    {
        return __('measure');
    }
    public static function getPluralModelLabel(): string
    {
        return __('measures');
    }

    public function form(Form $form): Form
    {
        $configs = PetMeasure::cases();
        $schema = [];
        foreach ($configs as $config) {
            $schema = array_merge($schema, [
                Forms\Components\Hidden::make("type-{$config->name}")->default($config->name)->visibleOn('create'),
                Forms\Components\TextInput::make("name-type-{$config->name}")
                    ->label(__('Type'))
                    ->default($config->value)->visibleOn('create')
                    ->dehydrated(false)
                    ->disabled(),
                Forms\Components\TextInput::make("value-{$config->name}")
                    ->label(__('Value'))
                    ->inputMode('decimal')
                    ->minValue(0)
                    ->visibleOn('create')
                    ->numeric()
            ]);
        }
        $schema = array_merge($schema, [
            Forms\Components\Hidden::make('type')->visibleOn('edit'),
            Forms\Components\TextInput::make('name-type')
                ->formatStateUsing(fn ($record) => $record ? PetMeasure::getUnit($record->type) : '')
                ->visibleOn(['edit', 'view'])
                ->dehydrated(false)
                ->disabled(),
            Forms\Components\TextInput::make('value')
                ->label(__('Value'))
                ->numeric()
                ->inputMode('decimal')
                ->minValue(0)
                ->visibleOn(['edit', 'view']),
            Forms\Components\DatePicker::make('date')->translateLabel()->native(false)->displayFormat(config('filament.date_format'))->required(),
            Forms\Components\TextInput::make('local')->translateLabel()->maxLength(50),
            Forms\Components\Select::make('person_id')->label(ucfirst(__('person')))
            ->options(Person::getPersonByFlag(['veterinary', 'medication_volunteer'])->toArray())->searchable()->columnSpanFull(),
            Forms\Components\Textarea::make('observation')->translateLabel()->maxLength(300)->columnSpanFull(),
        ]);
        return $form
            ->schema($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')->getStateUsing(fn ($record) => PetMeasure::getUnit($record->type))
                    ->translateLabel()
                    ->searchable(),
                Tables\Columns\ViewColumn::make('value')->view('filament.tables.columns.label-variation'),
                Tables\Columns\TextColumn::make('date')
                    ->translateLabel()
                    ->sortable()
                    ->date(config('filament.date_format')),
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
                })->using(function ($livewire, array $data) {
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
                })->modalHeading(__('filament-actions::create.single.modal.heading', ['label' => self::getTitle()])),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->modalHeading(fn ($record) => __('filament-actions::view.single.modal.heading', ['label' =>  self::getTitle()])),
                Tables\Actions\EditAction::make()->modalHeading(fn ($record) => __('filament-actions::edit.single.modal.heading', ['label' =>  self::getTitle()])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])->defaultSort('date', 'desc');
    }
}
