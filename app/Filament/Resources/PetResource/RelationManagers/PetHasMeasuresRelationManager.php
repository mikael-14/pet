<?php

namespace App\Filament\Resources\PetResource\RelationManagers;

use App\Models\PetHasMeasure;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

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
                Forms\Components\Hidden::make('value'.$key)->default($key),
                Forms\Components\TextInput::make('type-'.$key)
                    ->default($config['name'])
                    ->disabled(),
                Forms\Components\TextInput::make('value-display-'.$key)
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
                Tables\Actions\CreateAction::make()->beforeFormValidated(function () {
                    $state = $this->form->getState();
                    $a = 1;
                    // Runs before the form fields are validated when the form is submitted.
                }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
