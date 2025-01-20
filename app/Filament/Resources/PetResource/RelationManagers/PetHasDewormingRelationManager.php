<?php

namespace App\Filament\Resources\PetResource\RelationManagers;

use App\Models\Deworming;
use App\Models\Person;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;

class PetHasDewormingRelationManager extends RelationManager
{
    protected static string $relationship = 'pet_has_deworming';

    public static function getTitle($ownerRecord = null, $pageClass = null): string
    {
        return ucfirst(__('deworming'));
    }

    public static function getModelLabel(): ?string
    {
        return __('deworming');
    }
    public static function getPluralModelLabel(): string
    {
        return __('dewormings');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('deworming_id')
                ->allowHtml()
                ->searchable()
                ->preload()
                    ->options(self::getOptionWithHelp(Deworming::all()))
                    ->reactive()
                    ->afterStateUpdated(function (\Filament\Forms\Set $set, \Filament\Forms\Get $get, $state) {
                        if ($get('id') === null) {
                            $expire = Deworming::find($state)?->expire ?? 0;
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
                            $expire = Deworming::find($get('deworming_id'))?->expire ?? 0;
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
                        $expire = Deworming::find($get('deworming_id'))?->expire ?? 0;
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
                Tables\Columns\TextColumn::make('deworming.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('deworming.type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->sortable()
                    ->date(config('filament.date_format'))
                    ->searchable(),
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
                Tables\Actions\CreateAction::make()->modalHeading(__('filament-actions::create.single.modal.heading', ['label' => self::getTitle()])),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->modalHeading(fn ($record) => __('filament-actions::view.single.modal.heading', ['label' => $record->deworming()?->first()->name ?? self::getTitle()])),
                Tables\Actions\EditAction::make()->modalHeading(fn ($record) => __('filament-actions::edit.single.modal.heading', ['label' => $record->deworming()?->first()->name ?? self::getTitle()])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->groupedBulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])->defaultSort('date', 'desc');
    }

    //custom functions outside filament 
    public static function getOptionWithHelp(\Illuminate\Database\Eloquent\Collection $model)
    {
        return $model->mapWithKeys(function ($item) {
            switch ($item['type']) {
                case 'internal':
                    $second = 'Internal';
                    break;
                case 'external':
                    $second = 'External';
                    break;
                case 'internal and external':
                    $second = 'Internal and external';
                    break;
                default:
                    $second = $item['type'];
                    break;
            }
            return [$item['id'] => view('filament.components.select-with-second-text')
                ->with('first', $item['name'])
                ->with('second', $second)
                ->render()];
        });
    }
}
