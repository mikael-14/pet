<?php

namespace App\Filament\Resources\PetResource\RelationManagers;

use App\Models\Deworming;
use Carbon\Carbon;
use Closure;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PetHasDewormingRelationManager extends RelationManager
{
    protected static string $relationship = 'pet_has_deworming';

    protected static ?string $title = 'Deworming';

    protected static ?string $modelLabel  = 'deworming';

    protected static ?string $pluralModelLabel = 'dewormings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('dewormings_id')
                ->allowHtml()
                ->searchable()
                ->preload()
                    ->options(self::getOptionWithHelp(Deworming::all()))
                    ->reactive()
                    ->afterStateUpdated(function (Closure $set, Closure $get, $state) {
                        if ($get('id') === null) {
                            $expires = Deworming::find($state)?->expires ?? 0;
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
                            $expires = Deworming::find($get('dewormings_id'))?->expires ?? 0;
                            if ($expires > 0 && !empty($state)) {
                                $new_date_expires = Carbon::parse($state)->addDays($expires);
                                $set('expires_at', $new_date_expires);
                            }
                        }
                    })
                    ->required(),
                Forms\Components\DatePicker::make('expires_at')
                    ->helperText(function (Closure $get) {
                        $expires = Deworming::find($get('dewormings_id'))?->expires ?? 0;
                        if ($expires > 0) {
                            return "Default expiration in {$expires} days" . $get('id') . '';
                        }
                        return 'No expiration defined';
                    })
                    ->afterOrEqual('date')
                    ->displayFormat(config('filament.date_format')),
                Forms\Components\TextInput::make('local')->maxLength(50),
                Forms\Components\TextInput::make('application')->maxLength(100),
                Forms\Components\Textarea::make('observation')->maxLength(300)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('deworming.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('deworming.type')
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
                Tables\Columns\TextColumn::make('application')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('observation')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->modalHeading(__('filament-support::actions/create.single.modal.heading', ['label' => self::getTitle()])),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->modalHeading(fn ($record) => __('filament-support::actions/view.single.modal.heading', ['label' => $record->deworming()?->first()->name ?? self::getTitle()])),
                Tables\Actions\EditAction::make()->modalHeading(fn ($record) => __('filament-support::actions/edit.single.modal.heading', ['label' => $record->deworming()?->first()->name ?? self::getTitle()])),
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
