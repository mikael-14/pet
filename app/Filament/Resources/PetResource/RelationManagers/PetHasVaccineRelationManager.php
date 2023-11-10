<?php

namespace App\Filament\Resources\PetResource\RelationManagers;

use App\Models\Person;
use App\Models\Vaccine;
use Carbon\Carbon;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PetHasVaccineRelationManager extends RelationManager
{
    protected static string $relationship = 'pet_has_vaccine';

    protected static ?string $title = 'Vaccine';

    protected static ?string $modelLabel  = 'vaccine';

    protected static ?string $pluralModelLabel = 'vaccines';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('vaccine_id')
                    ->options(Vaccine::all()->pluck('name', 'id'))
                    ->reactive()
                    ->afterStateUpdated(function (\Filament\Forms\Set $set, \Filament\Forms\Get $get, $state) {
                        if ($get('id') === null) {
                            $expire = Vaccine::find($state)?->expire ?? 0;
                            if ($expire > 0 && !empty($get('date'))) {
                                $new_date_expire = Carbon::parse($get('date'))->addDays($expire);
                                $set('expire_at', $new_date_expire);
                            }
                        }
                    })
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\DatePicker::make('date')
                ->native(false)
                    ->displayFormat(config('filament.date_format'))
                    ->reactive()
                    ->afterStateUpdated(function (\Filament\Forms\Set $set, \Filament\Forms\Get $get, $state) {
                        if ($get('id') === null) {
                            $expire = Vaccine::find($get('vaccine_id'))?->expire ?? 0;
                            if ($expire > 0 && !empty($state)) {
                                $new_date_expire = Carbon::parse($state)->addDays($expire);
                                $set('expire_at', $new_date_expire);
                            }
                        }
                    })
                    ->required(),
                Forms\Components\DatePicker::make('expire_at')
                ->native(false)
                    ->helperText(function (\Filament\Forms\Get $get) {
                        $expire = Vaccine::find($get('vaccine_id'))?->expire ?? 0;
                        if ($expire > 0) {
                            return "Default expiration in {$expire} days" . $get('id') . '';
                        }
                        return 'No expiration defined';
                    })
                    ->afterOrEqual('date')
                    ->displayFormat(config('filament.date_format')),
                Forms\Components\TextInput::make('local')->maxLength(50),
                Forms\Components\Select::make('person_id')->options(Person::getPersonByFlag(['veterinary','medication_volunteer'])->toArray())->searchable(),
                Forms\Components\Textarea::make('observation')->maxLength(300)->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vaccine.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->sortable()
                    ->date(config('filament.date_format')),
                Tables\Columns\TextColumn::make('expire_at')
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
                Tables\Actions\CreateAction::make()->modalHeading(__('filament-support::actions/create.single.modal.heading', ['label' => self::$title])),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->modalHeading(fn ($record) => __('filament-support::actions/view.single.modal.heading', ['label' => $record->vaccine()?->first()->name ?? self::$title])),
                Tables\Actions\EditAction::make()->modalHeading(fn ($record) => __('filament-support::actions/edit.single.modal.heading', ['label' => $record->vaccine()?->first()->name ?? self::$title])),
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
