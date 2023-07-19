<?php

namespace App\Filament\Resources\PrescriptionResource\RelationManagers;

use App\Filament\Resources\PrescriptionResource\Pages\ViewPrescription;
use App\Models\Medicine;
use App\Models\PrescriptionHasMedicine;
use Carbon\Carbon;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;

class PrescriptionHasMedicinesRelationManager extends RelationManager
{
    protected static string $relationship = 'prescription_has_medicines';

    protected static ?string $title = 'Medicines';

    protected static ?string $modelLabel  = 'medicine';

    protected static ?string $pluralModelLabel = 'medicines';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(8)
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
                            ->required()
                            ->options(__('pet/prescriptionmedicines.status'))
                            ->columnSpan(3)
                            ->reactive()
                            ->default('active'),
                        Forms\Components\Toggle::make('emergency')
                            ->default(false)
                            ->inline(false)
                            ->reactive()
                            ->columnSpan(1)
                            ->required(),
                    ]),
                Forms\Components\TextInput::make('dosage')
                    ->required()
                    ->suffix(function(Closure $get){ $find = Medicine::find($get('medicine_id'))?->type; return $find ? __("pet/medicine.$find") : ''; })
                    ->reactive()
                    ->maxLength(50),
                Forms\Components\TextInput::make('frequency')
                    ->numeric()
                    ->mask(fn (Mask $mask) => $mask->pattern('00000'))
                    ->integer() // Disallow decimal numbers.
                    ->minValue(1)
                    ->required()
                    ->suffix('time in hours')
                    ->reactive(),
                Forms\Components\DateTimePicker::make('start_date')
                    ->displayFormat(config('filament.date_time_format'))
                    ->withoutSeconds()
                    ->minutesStep(15)
                    // ->reactive()
                    // ->afterStateUpdated(function (Closure $set, Closure $get, $state) {
                    //     if ($get('id') === null) {
                    //         $expire = Deworming::find($get('deworming_id'))?->expire ?? 0;
                    //         if ($expire > 0 && !empty($state)) {
                    //             $new_date_expire = Carbon::parse($state)->addDays($expire);
                    //             $set('expire_at', $new_date_expire);
                    //         }
                    //     }
                    // })
                    ->required(),
                Forms\Components\DateTimePicker::make('end_date')
                    ->afterOrEqual('start_date')
                    ->displayFormat(config('filament.date_time_format'))
                    ->withoutSeconds()
                    ->minutesStep(15),
                Placeholder::make('shout')
                    ->label(false)
                    ->content(function (Closure $get) {
                        $frequency = (int)$get('frequency');
                        if ($frequency > 0) {
                            $sos = $get('emergency');
                            $dosage = $get('dosage');
                            $type = $get('status');
                            $medicine = Medicine::where('id', (int)$get('medicine_id'))->first();
                            if ($frequency < 24) {
                                $totalTimes = intdiv(24, $frequency);
                            } else {
                                $totalTimes = intdiv($frequency, 24);
                            }
                            switch ($type) {
                                case 'canceled':
                                    $content = __('pet/prescriptionmedicines.shout.canceled', ['medicine' => $medicine->name ?? '']);
                                    break;
                                case 'on_hold':
                                    $content = __('pet/prescriptionmedicines.shout.on_hold', ['medicine' => $medicine->name ?? '']);
                                    break;
                                case 'completed':
                                    $content = __('pet/prescriptionmedicines.shout.completed', ['medicine' => $medicine->name ?? '']);
                                    break;
                                default:
                                    if ($frequency < 24) {
                                        $content = __('pet/prescriptionmedicines.shout.times_day', ['dosage' => $dosage, 'medicine' => $medicine->name ?? '', 'total_times' => $totalTimes]);
                                    } else {
                                        $content = __('pet/prescriptionmedicines.shout.every_days', ['dosage' => $dosage, 'medicine' => $medicine->name ?? '', 'total_times' => $totalTimes]);
                                    }
                                    break;
                            }

                            return view('filament.components.placeholder-alert')
                                ->with('content', $content)
                                ->with('type', $sos ? 'danger' : match ($type) {
                                    'active' => 'info',
                                    'on_hold' => 'warning',
                                    'completed' => 'success',
                                    'canceled' => 'danger',
                                });
                        }
                        return '';
                    })

                    ->hidden(fn (Closure $get): bool => empty($get('frequency')) || empty($get('medicine_id')))
                    ->columnSpan('full'),
                Forms\Components\Textarea::make('observation')
                    ->maxLength(200)
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('medicine.name')
                    ->color(fn (PrescriptionHasMedicine $record): string => $record->emergency ? 'danger' : '')
                    ->icon(fn (PrescriptionHasMedicine $record): string => $record->emergency ? 'uni-medical-square-o' : '')
                    ->iconPosition('after')
                    ->description(fn (PrescriptionHasMedicine $record): string => $record->emergency ? '(SOS) ' : '' . $record->observation ?? ''),
                Tables\Columns\BadgeColumn::make('status')
                    ->enum(__('pet/prescriptionmedicines.status'))
                    ->colors([
                        'primary' => 'active',
                        'warning' => 'on_hold',
                        'success' => 'completed',
                        'danger' => 'canceled',
                    ]),
                Tables\Columns\TextColumn::make('dosage'),
                Tables\Columns\TextColumn::make('frequency')
                    ->formatStateUsing(function (string $state): string {
                        if ($state < 24) {
                            return __('pet/prescriptionmedicines.shout.repeat_hour', ['frequency' => $state]);
                        } else {
                            $frequency = intdiv($state, 24);
                            return __('pet/prescriptionmedicines.shout.repeat_day', ['frequency' => $frequency]);
                        }
                    }),
                Tables\Columns\TextColumn::make('start_date')
                    ->sortable()
                    ->date(config('filament.date_format')),
                Tables\Columns\TextColumn::make('end_date')
                    ->placeholder('-')
                    ->sortable()
                    ->date(config('filament.date_format')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(__('pet/prescriptionmedicines.status'))
                    ->multiple(),
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
                    ->visible(fn ($livewire) => $livewire->pageClass !== ViewPrescription::class)
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
        return 'start_date';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }
}
