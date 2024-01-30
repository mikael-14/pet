<?php

namespace App\Filament\Resources;

use App\Enums\PetGender;
use App\Filament\Resources\PetResource\Pages;
use App\Filament\Resources\PetResource\RelationManagers;
use App\Models\Status;
use App\Models\Pet;
use App\Models\ShelterBlock;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PetResource extends Resource
{
    protected static ?string $model = Pet::class;

    protected static ?string $navigationIcon = 'tabler-paw';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 0;

    public static function getNavigationLabel(): string
    {
        return __('Pets');
    }
    public static function getModelLabel(): string
    {
        return __('pet');
    }


    const StyleCustomBadge = [
        'style' => 'padding: 0;width: max-content;margin-left: 1rem;',
        'class' => 'fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-w-[theme(spacing.6)] fi-color-custom bg-custom-50 text-custom-600 ring-custom-600/10 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/30'
    ];
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('image')->collection('pets-main-image')->translateLabel()->square(),
                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->searchable(),
                Tables\Columns\TextColumn::make('gender')
                    ->translateLabel()
                    ->badge()
                    ->iconPosition('after')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status.name')
                    ->translateLabel()
                    ->badge()
                    ->color('none')
                    ->extraAttributes(static function (Pet $record): array {
                        return ['style' => self::StyleCustomBadge['style'] . 'background-color:' . $record->status->color, 'class' => self::StyleCustomBadge['class']];
                    })
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('entry_status.name')
                    ->translateLabel()
                    ->badge()
                    ->color('none')
                    ->extraAttributes(static function (Pet $record): array {
                        return ['style' => self::StyleCustomBadge . 'background-color:' . $record->entry_status->color,  'class' => self::StyleCustomBadge['class']];
                    })
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('shelter_block.name')
                    ->translateLabel()
                    ->badge()
                    ->color('none')
                    ->extraAttributes(static function (Pet $record): array {
                        return ['style' => self::StyleCustomBadge . 'background-color:' . $record->shelter_block->color, 'class' => self::StyleCustomBadge['class']];
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('species')
                    ->translateLabel()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('chip')
                    ->translateLabel()
                    ->searchable(),
                Tables\Columns\TextColumn::make('chip_date')
                    ->translateLabel()
                    ->date(config('filament.date_format'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('highligh_tests')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state->name . ' - ' . __(ucfirst($state->result)))
                    ->color(fn ($state): string => match ($state->result) {
                        'unknown' => 'warning',
                        'positive' => 'danger',
                        'negative' => 'success',
                        default => 'primary',
                    })
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('birth_date')
                    ->translateLabel()
                    ->date(config('filament.date_format'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('entry_date')
                    ->translateLabel()
                    ->date(config('filament.date_format'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('sterilized')
                    ->translateLabel()
                    ->boolean(),
                Tables\Columns\TextColumn::make('color')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('coat')
                    ->translateLabel()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->translateLabel()
                    ->dateTime(config('filament.date_time_format'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->translateLabel()
                    ->dateTime(config('filament.date_time_format'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('gender')
                    ->translateLabel()
                    ->options(PetGender::class),
                Tables\Filters\SelectFilter::make('sterilized')
                    ->translateLabel()
                    ->options([
                        1 => __('Yes'),
                        0 => __('No'),
                    ]),
                Tables\Filters\SelectFilter::make('status_id')
                    ->label(__('Status'))
                    ->options(Status::all()->pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('shelter_block_id')
                    ->label(__('Shelter block'))
                    ->multiple()
                    ->options(ShelterBlock::all()->pluck('name', 'id')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }
    protected function shouldPersistTableFiltersInSession(): bool
    {
        return true;
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PetHasMedicineRelationManager::class,
            RelationManagers\PetHasDewormingRelationManager::class,
            RelationManagers\PetHasDietRelationManager::class,
            RelationManagers\PetHasMeasureRelationManager::class,
            RelationManagers\PetHasTestRelationManager::class,
            RelationManagers\PetHasVaccineRelationManager::class,
        ];
    }
    protected function shouldPersistTableColumnSearchInSession(): bool
    {
        return true;
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPets::route('/'),
            'create' => Pages\CreatePet::route('/create'),
            'edit' => Pages\EditPet::route('/{record}/edit'),
            'view' => Pages\ViewPet::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'chip',];
    }

    //custom functions outside filament 
    public static function getOptionWithColor(\Illuminate\Database\Eloquent\Collection $model)
    {
        return $model->mapWithKeys(function ($item) {
            return [$item['id'] => view('filament.components.select-with-color')
                ->with('name', $item['name'])
                ->with('color', $item['color'])
                ->render()];
        });
    }
    public static function getRadioWithColor(\Illuminate\Database\Eloquent\Collection $model)
    {
        return $model->mapWithKeys(function ($item) {
            return [
                $item['id'] => view('filament.components.select-with-color')
                    ->with('name', $item['name'])
                    ->with('color', $item['color'])
            ];
        });
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make()
                    ->schema([
                        Components\Split::make([
                            Components\Grid::make(1)->schema([
                                SpatieMediaLibraryImageEntry::make('image')
                                    ->label(false)
                                    ->collection('pets-main-image')
                                    ->width(300)
                                    ->height(300),
                                Components\TextEntry::make('highligh_tests')
                                    ->placeholder('-')
                                    ->badge()
                                    ->formatStateUsing(fn ($state) => $state->name . ' - ' . __(ucfirst($state->result)))
                                    ->color(fn ($state): string => match ($state->result) {
                                        'unknown' => 'warning',
                                        'positive' => 'danger',
                                        'negative' => 'success',
                                        default => 'primary',
                                    })
                                    ->translateLabel(),
                                Components\TextEntry::make('status.name')
                                    ->placeholder('-')
                                    ->badge()
                                    ->color('none')
                                    ->extraAttributes(static function (Pet $record): array {
                                        return ['style' => 'padding: 0;width: fit-content;background-color:' . $record->status->color, 'class' => 'fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-w-[theme(spacing.6)] fi-color-custom bg-custom-50 text-custom-600 ring-custom-600/10 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/30'];
                                    })
                                    ->translateLabel()
                            ])->grow(false),
                            Components\Grid::make(5)
                                ->schema([
                                    Components\TextEntry::make('name')
                                        ->translateLabel(),
                                    Components\TextEntry::make('species')
                                        ->translateLabel(),
                                    Components\TextEntry::make('gender')
                                        ->translateLabel()
                                        ->badge()
                                        ->iconPosition('after'),
                                    Components\TextEntry::make('birth_date')->placeholder('-')
                                        ->translateLabel()
                                        ->formatStateUsing(
                                            function ($state): string {
                                                if ($state) {
                                                    $ageInYears = $state->diffInYears();
                                                    $ageInMonths = $state->diffInMonths() - ($ageInYears * 12);
                                                    $string = $state->format(config('filament.date_format'));
                                                    $string .= ' (';
                                                    if ($ageInYears > 0) {
                                                        $string .= ' ' . trans_choice('age_years', $ageInYears, ['value' => $ageInYears]);
                                                    }
                                                    if ($ageInMonths > 0) {
                                                        $string .= ' ' . trans_choice('age_months', $ageInMonths, ['value' => $ageInMonths]);
                                                    }
                                                    $string .= ' )';
                                                    return $string;
                                                }
                                                return '-';
                                            }
                                        ),
                                    Components\TextEntry::make('chip')->placeholder('-')->translateLabel(),
                                    Components\TextEntry::make('chip_date')->placeholder('-')->dateTime(config('filament.date_format'))->translateLabel(),
                                    Components\TextEntry::make('color')->placeholder('-')->translateLabel(),
                                    Components\TextEntry::make('coat')->placeholder('-')->translateLabel(),
                                    Components\TextEntry::make('breed')->placeholder('-')->translateLabel(),
                                    Components\IconEntry::make('adoptable')->translateLabel()
                                        ->boolean(),
                                    Components\TextEntry::make('shelter_block.name')->placeholder('-')->translateLabel(),
                                    Components\TextEntry::make('entry_status.name')->placeholder('-')->translateLabel(),
                                    Components\TextEntry::make('entry_date')->placeholder('-')->translateLabel()->dateTime(config('filament.date_format')),
                                    Components\IconEntry::make('sterilized')
                                        ->translateLabel()
                                        ->boolean(),
                                    Components\TextEntry::make('sterilized_date')->placeholder('-')->translateLabel()->dateTime(config('filament.date_format')),
                                    Components\TextEntry::make('sterilized_local')->placeholder('-')->translateLabel(),
                                ]),

                        ])->from('sm'),
                    ]),
                Components\Section::make('Observation')
                    ->heading(__('Observation'))
                    ->schema([
                        Components\TextEntry::make('observation')
                            ->label(false)
                            ->markdown(),
                    ])
                    ->collapsed(false)
            ]);
    }
}
