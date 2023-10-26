<?php

namespace App\Filament\Resources\PetResource\RelationManagers;

use App\Models\Medicine;
use App\Models\Person;
use App\Models\PetHasMedicine;
use Carbon\Carbon;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PetHasMedicineRelationManager extends RelationManager
{
    protected static string $relationship = 'pet_has_medicine';

    protected static ?string $title = 'Medicine';

    protected static ?string $modelLabel  = 'medicine';

    protected static ?string $pluralModelLabel = 'medicines';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(8)
                    ->schema([
                        Forms\Components\Select::make('medicine_id')
                            ->required()
                            ->options(Medicine::all()->mapWithKeys(function ($medicine) {
                                return [$medicine->id => $medicine->name . ' - ' . __("pet/medicine.$medicine->type")];
                            }))
                            ->columnSpan(4)
                            ->reactive()
                            ->searchable(),
                        Forms\Components\Select::make('status')
                            ->options(__('pet/prescriptionmedicines.status'))
                            ->columnSpan(3)
                            ->default('active'),
                        Forms\Components\Toggle::make('emergency')
                            ->default(false)
                            ->inline(false)
                            ->columnSpan(1)
                            ->required(),
                    ]),
                Forms\Components\TextInput::make('dosage')
                    ->required()
                    ->suffix(function (\Filament\Forms\Get $get) {
                        $find = Medicine::find($get('medicine_id'))?->type;
                        return $find ? __("pet/medicine.$find") : '';
                    })
                    ->maxLength(50),
                Forms\Components\DateTimePicker::make('date')
                    ->displayFormat(config('filament.date_time_format'))
                    ->default(Carbon::now(config('app.timezone'))->format('Y-m-d H:00:00'))
                    ->withoutSeconds()
                    ->minutesStep(15)
                    ->reactive()
                    ->required(),
                Forms\Components\Select::make('person_id')
                    ->options(Person::getPersonByFlag(['veterinary', 'medication_volunteer'])->toArray())
                    ->searchable()
                    ->preload()
                    ->required(fn (\Filament\Forms\Get $get) => Carbon::parse($get('date'))->lessThan(Carbon::now())),
                Forms\Components\Toggle::make('administered')
                    ->default(true)
                    ->inline(false)
                    ->required(),
                Forms\Components\Textarea::make('observation')
                    ->maxLength(200)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('medicine.name')
                    ->color(fn (PetHasMedicine $record): string => $record->emergency ? 'danger' : '')
                    ->icon(fn (PetHasMedicine $record): string => $record->emergency ? 'uni-medical-square-o' : '')
                    ->iconPosition('after')
                    ->description(fn (PetHasMedicine $record): string => $record->emergency ? '(SOS) ' : '' . $record->observation ?? ''),
                Tables\Columns\TextColumn::make('dosage'),
                Tables\Columns\TextColumn::make('person.name'),
                Tables\Columns\TextColumn::make('date')
                    ->sortable()
                    ->date(config('filament.date_time_format')),
                Tables\Columns\IconColumn::make('administered')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('administered')
                    ->options([
                        1 => 'Yes',
                        0 => 'No',
                    ]),
                Tables\Filters\SelectFilter::make('emergency')
                    ->options([
                        0 => 'No',
                        1 => 'Yes',
                    ]),
                Tables\Filters\SelectFilter::make('medicine_id')
                    ->relationship('medicine', 'name')
                    ->searchable()
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalHeading(__('filament-support::actions/create.single.modal.heading', ['label' => self::getTitle()])),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->modalHeading(fn ($record) => __('filament-support::actions/view.single.modal.heading', ['label' => $record->medicine()?->first()->name ?? self::getTitle()])),
                Tables\Actions\EditAction::make()->modalHeading(fn ($record) => __('filament-support::actions/edit.single.modal.heading', ['label' => $record->medicine()?->first()->name ?? self::getTitle()])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }
}
