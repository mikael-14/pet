<?php

namespace App\Filament\Resources\PrescriptionResource\RelationManagers;

use App\Filament\Resources\PrescriptionResource\Pages\ViewPrescription;
use App\Models\Medicine;
use App\Models\PrescriptionHasMedicine;
use Carbon\Carbon;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Components\ViewField;

class PrescriptionHasMedicinesRelationManager extends RelationManager
{
    protected static string $relationship = 'prescription_has_medicines';

    protected static ?string $title = 'Medicines';

    protected static ?string $modelLabel  = 'medicine';

    protected static ?string $pluralModelLabel = 'medicines';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Medicine')
                            ->schema([
                                Forms\Components\Select::make('medicine_id')
                                    ->required()
                                    ->options(Medicine::all()->mapWithKeys(function ($medicine) {
                                        return [$medicine->id => $medicine->name . ' - ' . __("pet/medicine.$medicine->type")];
                                    }))
                                    ->live(onBlur: true)
                                    ->disabled(fn ($context) => $context !== 'create')
                                    ->searchable(),
                                Forms\Components\TextInput::make('dosage')
                                    ->required()
                                    ->disabled(fn ($context) => $context !== 'create')
                                    ->suffix(function (\Filament\Forms\Get $get) {
                                        $find = Medicine::find($get('medicine_id'))?->type;
                                        return $find ? __("pet/medicine.$find") : '';
                                    })
                                    ->lazy()
                                    ->maxLength(50),
                                Grid::make(8)->schema([
                                    Forms\Components\TextInput::make('frequency')
                                        ->numeric()
                                        ->mask('99999')
                                        ->integer() // Disallow decimal numbers.
                                        ->minValue(1)
                                        ->suffix('time in hours')
                                        ->lazy()
                                        ->live(onBlur: true)
                                        ->disabled(fn ($context) => $context !== 'create')
                                        ->columnSpan(4),
                                    Forms\Components\Select::make('status')
                                        ->selectablePlaceholder(false)
                                        ->required()
                                        ->options(__('pet/prescriptionmedicines.status'))
                                        ->live(onBlur: true)
                                        ->default('active')
                                        ->columnSpan(3),
                                    Forms\Components\Toggle::make('emergency')
                                        ->default(false)
                                        ->inline(false)
                                        ->live(onBlur: true)
                                        ->required()
                                        ->columnSpan(1),
                                ]),
                                Forms\Components\DateTimePicker::make('start_date')
                                    ->native(false)
                                    ->displayFormat(config('filament.date_time_format'))
                                    ->seconds(false)
                                    ->minutesStep(15)
                                    ->disabled(fn ($context) => $context !== 'create')
                                    ->live(debounce: 500)
                                    ->required(),
                                Forms\Components\DateTimePicker::make('end_date')
                                    ->native(false)
                                    ->afterOrEqual('start_date')
                                    ->displayFormat(config('filament.date_time_format'))
                                    ->seconds(false)
                                    ->minutesStep(15)
                                    ->live(debounce: 500)
                                    ->required(fn (Forms\Get $get): bool => filled($get('frequency'))),
                                Placeholder::make('shout')
                                    ->label(false)
                                    ->content(function (\Filament\Forms\Get $get) {
                                        $frequency = (int)$get('frequency');
                                        $sos = $get('emergency');
                                        $type = $get('status');
                                        $dosage = $get('dosage');
                                        $medicine = Medicine::where('id', (int)$get('medicine_id'))->first();
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
                                                if ($frequency === 0) {
                                                    $content = __('pet/prescriptionmedicines.shout.one_take', ['medicine' => $medicine->name ?? '']);
                                                    break;
                                                }
                                                $date1 = Carbon::create($get('start_date'));
                                                $date2 = Carbon::create($get('end_date'));
                                                $diffdates = $date2->diffInHours($date1);
                                                $takes = 1;
                                                if ($get('end_date') && $get('start_date')) {
                                                    $takes = floor($diffdates / $frequency) + 1;
                                                }

                                                if ($frequency < 24) {
                                                    $totalTimes = intdiv(24, $frequency);
                                                    $content = __('pet/prescriptionmedicines.shout.times_day', ['dosage' => $dosage, 'medicine' => $medicine->name ?? '', 'total_times' => $totalTimes, 'takes' => $takes]);
                                                    break;
                                                }
                                                $totalTimes = intdiv($frequency, 24);
                                                $content = __('pet/prescriptionmedicines.shout.every_days', ['dosage' => $dosage, 'medicine' => $medicine->name ?? '', 'total_times' => $totalTimes, 'takes' => $takes]);
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
                                    })
                                    ->visible(fn (\Filament\Forms\Get $get): bool => (bool)$get('medicine_id'))
                                    ->columnSpan('full'),
                                Forms\Components\Textarea::make('observation')
                                    ->maxLength(200)
                                    ->autosize()
                                    ->columnSpanFull(),
                            ])->columns(2),
                        Forms\Components\Tabs\Tab::make('Scheduled')
                            ->schema([
                                ViewField::make('rating')
                                    ->view('filament.components.table-pet-has-medicines')
                                    ->columnSpan('full')
                            ])->hiddenOn('create')
                            ->badge(fn ($record) => $record->pet_has_medicines->count())
                            ->badgeColor('primary'),
                    ])->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('medicine.name')
                    ->color(fn (PrescriptionHasMedicine $record): string => $record->emergency ? 'danger' : '')
                    ->icon(fn (PrescriptionHasMedicine $record): string => $record->emergency ? 'uni-medical-square-o' : '')
                    ->iconPosition('after')
                    ->description(fn (PrescriptionHasMedicine $record): string|null => ($record->emergency ? '(SOS) ' : '') . \Illuminate\Support\Str::limit($record->observation, 25))
                    ->tooltip(fn (PrescriptionHasMedicine $record): string|null  => ($record->emergency ? '(SOS) ' : '') . $record->observation),
                Tables\Columns\TextColumn::make('start_date')
                    ->sortable()
                    ->date(config('filament.date_time_format')),
                Tables\Columns\TextColumn::make('end_date')
                    ->placeholder('-')
                    ->sortable()
                    ->date(config('filament.date_time_format')),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string =>  __('pet/prescriptionmedicines.status')[$state] ?? '-')
                    ->colors([
                        'primary' => 'active',
                        'warning' => 'on_hold',
                        'success' => 'completed',
                        'danger' => 'canceled',
                    ]),
                Tables\Columns\TextColumn::make('dosage'),
                Tables\Columns\TextColumn::make('frequency')
                    ->formatStateUsing(function (string|null $state): string {
                        $value = (int)$state;
                        if ($value === 0) {
                            return '-';
                        }
                        if ($value < 24) {
                            return __('pet/prescriptionmedicines.shout.repeat_hour', ['frequency' => $value]);
                        }
                        $frequency = intdiv($value, 24);
                        return __('pet/prescriptionmedicines.shout.repeat_day', ['frequency' => $frequency]);
                    }),
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
                Tables\Actions\EditAction::make('edit-modal')
                    ->visible(fn ($livewire) => $livewire->pageClass !== ViewPrescription::class),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn ($livewire) => $livewire->pageClass !== ViewPrescription::class),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('change_status')
                    ->icon('heroicon-s-pencil')
                    ->action(function (Collection $records, array $data): void {
                        if (isset($data['new_status']))
                            foreach ($records as $record) {
                                $record['status'] = $data['new_status'];
                                $record->save();
                            }
                        Notification::make()
                            ->title('All status changed')
                            ->success()
                            ->send();
                    })
                    ->form([
                        Forms\Components\Select::make('new_status')
                            ->options(__('pet/prescriptionmedicines.status'))
                            ->required()
                    ])
                    ->deselectRecordsAfterCompletion(),

                Tables\Actions\DeleteBulkAction::make(),
            ])->defaultSort('start_date', 'asc');
    }
}
