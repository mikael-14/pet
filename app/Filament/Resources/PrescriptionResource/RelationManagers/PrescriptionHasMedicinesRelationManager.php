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
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

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

                Forms\Components\Select::make('medicine_id')
                    ->required()
                    ->options(Medicine::all()->mapWithKeys(function ($medicine) {
                        return [$medicine->id => $medicine->name . ' - ' . __("pet/medicine.$medicine->type")];
                    }))

                    ->reactive()
                    ->searchable(),
                Forms\Components\TextInput::make('dosage')
                    ->required()
                    ->suffix(function (\Filament\Forms\Get $get) {
                        $find = Medicine::find($get('medicine_id'))?->type;
                        return $find ? __("pet/medicine.$find") : '';
                    })
                    ->lazy()
                    ->maxLength(50),
                Grid::make(8)->schema([
                    Forms\Components\TextInput::make('frequency')
                        ->numeric()
                        ->mask(fn (Mask $mask) => $mask->pattern('00000'))
                        ->integer() // Disallow decimal numbers.
                        ->minValue(1)
                        ->suffix('time in hours')
                        ->lazy()
                        ->columnSpan(4),
                    Forms\Components\Select::make('status')
                        ->disablePlaceholderSelection()
                        ->required()
                        ->options(__('pet/prescriptionmedicines.status'))
                        ->reactive()
                        ->default('active')
                        ->columnSpan(3),
                    Forms\Components\Toggle::make('emergency')
                        ->default(false)
                        ->inline(false)
                        ->reactive()
                        ->required()
                        ->columnSpan(1),
                ]),
                Forms\Components\DateTimePicker::make('start_date')
                    ->displayFormat(config('filament.date_time_format'))
                    ->withoutSeconds()
                    ->minutesStep(15)
                    ->required(),
                Forms\Components\DateTimePicker::make('end_date')
                    ->afterOrEqual('start_date')
                    ->displayFormat(config('filament.date_time_format'))
                    ->withoutSeconds()
                    ->minutesStep(15),
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
                                if ($frequency < 24) {
                                    $totalTimes = intdiv(24, $frequency);
                                    $content = __('pet/prescriptionmedicines.shout.times_day', ['dosage' => $dosage, 'medicine' => $medicine->name ?? '', 'total_times' => $totalTimes]);
                                    break;
                                }
                                $totalTimes = intdiv($frequency, 24);
                                $content = __('pet/prescriptionmedicines.shout.every_days', ['dosage' => $dosage, 'medicine' => $medicine->name ?? '', 'total_times' => $totalTimes]);
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
                    ->columnSpanFull(),

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
                Tables\Actions\EditAction::make()
                    ->visible(fn ($livewire) => $livewire->pageClass !== ViewPrescription::class),
                Tables\Actions\ReplicateAction::make()
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray'),
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
