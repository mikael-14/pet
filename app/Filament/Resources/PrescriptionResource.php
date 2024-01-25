<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrescriptionResource\Pages;
use App\Filament\Resources\PrescriptionResource\RelationManagers;
use App\Models\Clinic;
use App\Models\Medicine;
use App\Models\Person;
use App\Models\Pet;
use App\Models\Prescription;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Awcodes\FilamentBadgeableColumn\Components\Badge;
use Awcodes\FilamentBadgeableColumn\Components\BadgeableColumn;


class PrescriptionResource extends Resource
{
    protected static ?string $model = Prescription::class;

    protected static ?string $navigationIcon = 'tabler-report-medical';

    protected static ?string $slug = 'medication/prescriptions';

    public static function getNavigationGroup(): ?string
    {
        return __('Medications');
    }

    public static function getNavigationLabel(): string
    {
        return __('prescriptions');
    }
    public static function getModelLabel(): string
    {
        return __('prescription');
    }
    public static function getPluralModelLabel(): string
    {
        return __('prescriptions');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('pet_id')
                            ->allowHtml()
                            ->searchable()
                            ->preload()
                            ->getSearchResultsUsing(function (string $search) {
                                $pets = Pet::where('name', 'like', "%{$search}%")
                                    ->orWhere('chip', 'like', "{$search}%")
                                    ->limit(10)
                                    ->get();
                                return $pets->mapWithKeys(function ($pet) {
                                    return [$pet->getKey() => static::getOptionPet($pet)];
                                })->toArray();
                            })
                            ->getOptionLabelUsing(function ($value): string {
                                $pet = Pet::find($value);
                                return static::getOptionPet($pet);
                            })
                            ->options(function () {
                                $pets = Pet::orderBy('created_at', 'desc')
                                    ->limit(10)
                                    ->get();
                                return $pets->mapWithKeys(function ($pet) {
                                    return [$pet->getKey() => static::getOptionPet($pet)];
                                })->toArray();
                            })
                            ->required(),
                        Forms\Components\Select::make('person_id')
                            ->allowHtml()
                            ->searchable()
                            ->preload()
                            ->getSearchResultsUsing(function (string $search) {
                                $persons = Person::searchPerson(['veterinary', 'medication_volunteer'], $search);
                                return $persons->mapWithKeys(function ($person) {
                                    return [$person->getKey() => static::getOptionPerson($person)];
                                })->toArray();
                            })
                            ->getOptionLabelUsing(function ($value): string {
                                $person = Person::find($value);
                                return static::getOptionPerson($person);
                            })
                            ->options(function () {
                                $persons = Person::searchPerson(['veterinary', 'medication_volunteer']);
                                return $persons->mapWithKeys(function ($person) {
                                    return [$person->getKey() => static::getOptionPerson($person)];
                                })->toArray();
                            })
                            ->required(),
                        Forms\Components\Select::make('clinic_id')
                            ->searchable()
                            ->options(Clinic::limit(10)->pluck('name', 'id')),
                        Forms\Components\DatePicker::make('date')
                            ->native(false)
                            ->displayFormat(config('filament.date_format'))
                            ->default(now())
                            ->required(),
                        SpatieMediaLibraryFileUpload::make('file')
                            ->disk('petsPrescriptions')
                            ->collection('pets-prescriptions')
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
                            ->columnSpan('full')
                            ->hiddenOn('view'),
                        Forms\Components\Textarea::make('observation')
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pet.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('number')
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('clinic.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('person.name')
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->date(config('filament.date_format'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('medicine_start_date')
                    ->dateTime(config('filament.date_time_format'))
                    ->sortable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('medicine_end_date')
                    ->dateTime(config('filament.date_time_format'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->placeholder('-'),
                BadgeableColumn::make('count_medicines')
                    ->label('Medicines')
                    ->suffixBadges(function ($record) {
                        return $record->prescription_has_medicines->map(function ($medicines) {
                            return Badge::make($medicines->medicine->name)->color(fn () => match ($medicines->status) {
                                default => 'primary',
                                'unstarted' => 'gray',
                                'on_hold' => 'warning',
                                'active' => 'info',
                                'completed' => 'success',
                                'canceled' => 'danger',
                            });
                        });
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(config('filament.date_time_format'))->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime(config('filament.date_time_format'))->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('pet_id')
                    ->label(ucfirst(__('pet')))
                    ->relationship('pet', 'name')
                    ->searchable(),
                Tables\Filters\SelectFilter::make('medicines.id')
                    ->label(ucfirst(__('medicines')))
                    ->relationship('medicines', 'name')
                    ->multiple()
                    ->searchable(),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('file')
                    ->label(__('View file'))
                    ->color('info')
                    ->url(fn (Prescription $record) => $record->getMedia('pets-prescriptions')[0]?->getFullUrl())
                    ->visible(fn (Prescription $record): bool => isset($record->getMedia('pets-prescriptions')[0]) ? true : false)
                    ->openUrlInNewTab()
                    ->icon('tabler-file-download'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //Tables\Actions\DeleteBulkAction::make(),
                //Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PrescriptionHasMedicinesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrescriptions::route('/'),
            'create' => Pages\CreatePrescription::route('/create'),
            'edit' => Pages\EditPrescription::route('/{record}/edit'),
            'view' => Pages\ViewPrescription::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
    public static function getOptionPet(Pet $model): string
    {
        $mediaCollection = $model->getMedia('pets-main-image');
        $image = $mediaCollection->isNotEmpty() ? $mediaCollection[0]->getUrl() : '';
        return
            view('filament.components.select-with-image')
            ->with('label', $model?->name)
            ->with('description', $model?->chip)
            ->with('image', $image)
            ->render();
    }
    public static function getOptionPerson(Person $model): string
    {
        $flags = $model?->person_flags->pluck('name');
        $flags = array_map( function ($flag) {
            return __("pet/personflags{$flag->value}");
        },$flags);
        return
            view('filament.components.select-with-image')
            ->with('label', $model?->name)
            ->with('description', $model?->person_flags->pluck('name')->implode(','))
            ->with('image', false)
            ->render();
    }
}
