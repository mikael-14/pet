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
use Illuminate\Support\HtmlString;

class PetHasMedicineRelationManager extends RelationManager
{
    protected static string $relationship = 'pet_has_medicine';

    public static function getTitle($ownerRecord = null, $pageClass = null): string
    {
        return ucfirst(__('medicine'));
    }

    public static function getModelLabel(): string
    {
        return __('medicine');
    }
    public static function getPluralModelLabel(): string
    {
        return __('medicines');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(8)
                    ->schema([
                        Forms\Components\Select::make('medicine_id')
                            ->required()
                            ->options(Medicine::all()->mapWithKeys(function ($medicine) {
                                return [$medicine->id => $medicine->name . ' - ' . $medicine->type->getLabel()];
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
                    ->native(false)
                    ->displayFormat(config('filament.date_time_format'))
                    ->default(Carbon::now(config('app.timezone'))->format('Y-m-d 00:00:00'))
                    ->seconds(false)
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
                Forms\Components\Placeholder::make('documentation')
                    ->content(new HtmlString('<a href="https://filamentphp.com/docs" class="text text-primary">filamentphp.com</a>'))
                    ,
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
                    ->description(fn (PetHasMedicine $record): string|null => ($record->emergency ? '(SOS) ' : '') . \Illuminate\Support\Str::limit($record->observation, 25))
                    ->tooltip(fn (PetHasMedicine $record): string|null  => ($record->emergency ? '(SOS) ' : '') . $record->observation),
                Tables\Columns\TextColumn::make('date')
                    ->sortable()
                    ->date(config('filament.date_time_format')),
                Tables\Columns\IconColumn::make('administered')
                    ->boolean(),
                Tables\Columns\TextColumn::make('dosage'),
                Tables\Columns\TextColumn::make('person.name'),
            ])
            ->filters([
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DateTimePicker::make('from')
                             ->native(false)
                            ->displayFormat(config('filament.date_time_format'))
                            ->default(Carbon::now(config('app.timezone'))->format('Y-m-d 00:00:00'))
                            ->seconds(false)
                            ->minutesStep(15),
                        Forms\Components\DateTimePicker::make('until')
                             ->native(false)
                            ->displayFormat(config('filament.date_time_format'))
                            ->seconds(false)
                            ->minutesStep(15),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    })->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('From ' . Carbon::parse($data['from'])->format(config('filament.date_time_format')))
                                ->removeField('from');
                        }
                 
                        if ($data['until'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('Until ' . Carbon::parse($data['until'])->format(config('filament.date_time_format')))
                                ->removeField('until');
                        }
                 
                        return $indicators;
                    }),
                Tables\Filters\SelectFilter::make('administered')
                    ->options([
                        1 => 'Yes',
                        0 => 'No',
                    ])->label('Administered'),
                Tables\Filters\SelectFilter::make('emergency')
                    ->options([
                        0 => 'No',
                        1 => 'Yes',
                    ])->label('emergency'),
                Tables\Filters\SelectFilter::make('medicine_id')
                    ->relationship('medicine', 'name')
                    ->searchable()->label('Medicine')
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalHeading(__('filament-actions::create.single.modal.heading', ['label' => self::getTitle()])),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->modalHeading(fn ($record) => __('filament-actions::view.single.modal.heading', ['label' => $record->medicine()?->first()->name ?? self::getTitle()])),
                Tables\Actions\EditAction::make()->modalHeading(fn ($record) => __('filament-actions::edit.single.modal.heading', ['label' => $record->medicine()?->first()->name ?? self::getTitle()])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }
}
