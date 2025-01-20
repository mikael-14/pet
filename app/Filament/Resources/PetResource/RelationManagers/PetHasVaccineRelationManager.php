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

    public static function getTitle($ownerRecord = null, $pageClass = null): string
    {
        return ucfirst(__('vaccine'));
    }

    public static function getModelLabel(): ?string
    {
        return __('vaccine');
    }
    public static function getPluralModelLabel(): string
    {
        return __('vaccines');
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('vaccine_id')
                ->label(ucfirst(__('vaccine')))
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
                ->translateLabel()
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
                ->translateLabel()
                ->native(false)
                    ->helperText(function (\Filament\Forms\Get $get): string {
                        $expire = Vaccine::find($get('vaccine_id'))?->expire ?? 0;
                        if ($expire > 0) {
                            return trans_choice('default_expiration', $expire, ['days' => $expire]);
                        }
                        return '';
                    })
                    ->afterOrEqual('date')
                    ->displayFormat(config('filament.date_format')),
                Forms\Components\TextInput::make('local')->translateLabel()->maxLength(50),
                Forms\Components\Select::make('person_id')->label(ucfirst(__('person')))->options(Person::getPersonByFlag(['veterinary','medication_volunteer'])->toArray())->searchable(),
                Forms\Components\Textarea::make('observation')->translateLabel()->maxLength(300)->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vaccine.name')
                ->label(ucfirst(__('vaccine')))
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                ->translateLabel()
                    ->sortable()
                    ->date(config('filament.date_format')),
                Tables\Columns\TextColumn::make('expire_at')
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
                Tables\Actions\CreateAction::make()->modalHeading(__('filament-actions::create.single.modal.heading', ['label' => self::getTitle()])),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->modalHeading(fn ($record) => __('filament-actions::view.single.modal.heading', ['label' => $record->vaccine()?->first()->name ?? self::getTitle()])),
                Tables\Actions\EditAction::make()->modalHeading(fn ($record) => __('filament-actions::edit.single.modal.heading', ['label' => $record->vaccine()?->first()->name ?? self::getTitle()])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->groupedBulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])->defaultSort('date', 'desc');
    }
 
}
