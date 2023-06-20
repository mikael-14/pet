<?php

namespace App\Filament\Resources\PetResource\RelationManagers;

use App\Models\Person;
use App\Models\Vaccine;
use Carbon\Carbon;
use Closure;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PetHasVaccineRelationManager extends RelationManager
{
    protected static string $relationship = 'pet_has_vaccine';

    protected static ?string $title = 'Vaccine';

    protected static ?string $modelLabel  = 'vaccine';

    protected static ?string $pluralModelLabel = 'vaccines';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('vaccines_id')
                    ->options(Vaccine::all()->pluck('name', 'id'))
                    ->reactive()
                    ->afterStateUpdated(function (Closure $set, Closure $get, $state) {
                        if ($get('id') === null) {
                            $expires = Vaccine::find($state)?->expires ?? 0;
                            if ($expires > 0 && !empty($get('date'))) {
                                $new_date_expires = Carbon::parse($get('date'))->addDays($expires);
                                $set('expires_at', $new_date_expires);
                            }
                        }
                    })
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->displayFormat(config('filament.date_format'))
                    ->reactive()
                    ->afterStateUpdated(function (Closure $set, Closure $get, $state) {
                        if ($get('id') === null) {
                            $expires = Vaccine::find($get('vaccines_id'))?->expires ?? 0;
                            if ($expires > 0 && !empty($state)) {
                                $new_date_expires = Carbon::parse($state)->addDays($expires);
                                $set('expires_at', $new_date_expires);
                            }
                        }
                    })
                    ->required(),
                Forms\Components\DatePicker::make('expires_at')
                    ->helperText(function (Closure $get) {
                        $expires = Vaccine::find($get('vaccines_id'))?->expires ?? 0;
                        if ($expires > 0) {
                            return "Default expiration in {$expires} days" . $get('id') . '';
                        }
                        return 'No expiration defined';
                    })
                    ->afterOrEqual('date')
                    ->displayFormat(config('filament.date_format')),
                Forms\Components\TextInput::make('local')->maxLength(50),
                Forms\Components\Select::make('people_id')->options(Person::getPersonByFlag(['veterinary','medication_volunteer']))->searchable(),
                Forms\Components\Textarea::make('observation')->maxLength(300)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vaccine.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->sortable()
                    ->date(config('filament.date_format')),
                Tables\Columns\TextColumn::make('expires_at')
                    ->sortable()
                    ->date(config('filament.date_format')),
                Tables\Columns\TextColumn::make('local')
                    ->sortable()
                    ->toggleable(),
                    Tables\Columns\TextColumn::make('person.name')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('observation')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('people_id')
                ->relationship('person', 'name')
                ->searchable()
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->modalHeading(__('filament-support::actions/create.single.modal.heading', ['label' => self::getTitle()])),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->modalHeading(fn ($record) => __('filament-support::actions/view.single.modal.heading', ['label' => $record->vaccine()?->first()->name ?? self::getTitle()])),
                Tables\Actions\EditAction::make()->modalHeading(fn ($record) => __('filament-support::actions/edit.single.modal.heading', ['label' => $record->vaccine()?->first()->name ?? self::getTitle()])),
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
