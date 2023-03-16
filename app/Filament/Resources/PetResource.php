<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PetResource\Pages;
use App\Filament\Resources\PetResource\RelationManagers;
use App\Models\Pet;
use App\Models\PetStatus;
use App\Models\PetLocation;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PetResource extends Resource
{
    protected static ?string $model = Pet::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 0;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Card::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Select::make('species')
                                    ->options(
                                        config('pet-species')
                                    )->disablePlaceholderSelection(),
                                Forms\Components\Select::make('gender')
                                    ->options([
                                        'male' => 'Male',
                                        'female' => 'Female',
                                    ])->required(),
                                Forms\Components\DatePicker::make('birth_date'),
                                Forms\Components\TextInput::make('chip')
                                    ->maxLength(20)
                                    ->hint(fn ($state) => 'Digits: ' . strlen($state) . '')
                                    ->lazy(),
                                Forms\Components\DatePicker::make('chip_date'),
                                Forms\Components\TextInput::make('color')
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('coat')
                                    ->maxLength(50),
                                Forms\Components\RichEditor::make('observation')->columnSpan('full'),
                            ])->columns(2),
                    ])->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Image')
                            ->schema([
                                Forms\Components\SpatieMediaLibraryFileUpload::make('image')->acceptedFileTypes(['image/*'])
                                    ->enableOpen()
                                    ->enableDownload()
                                    ->columnSpan('full'),
                            ]),
                        Forms\Components\Section::make('Status')
                            ->schema([
                                Forms\Components\Select::make('pet_statuses_id')
                                    ->allowHtml()
                                    ->searchable()
                                    ->preload()
                                    ->options(
                                        self::getOptionWithColor(PetStatus::all())
                                    )->required(),
                                Forms\Components\Select::make('pet_locations_id')
                                    ->allowHtml()
                                    ->searchable()
                                    ->preload()
                                    ->options(
                                        self::getOptionWithColor(PetLocation::all())
                                    )->required(),
                                Forms\Components\DatePicker::make('entry_date')
                                    ->required(),
                                Forms\Components\Toggle::make('sterilized')
                                    ->inline(false)->reactive(),
                                Forms\Components\DatePicker::make('sterilized_date')
                                    ->visible(fn ($get) => $get('sterilized')),
                                Forms\Components\TextInput::make('sterilized_local')
                                    ->visible(fn ($get) => $get('sterilized'))
                                    ->maxLength(50),
                            ]),
                    ])->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('image')->square(),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('gender')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('pet_status.name')->extraAttributes(static function (Pet $record): array {
                    return ['style' => 'background-color:' . $record->pet_status->color, 'class' => 'table-text-column-badge'];
                })

                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('pet_location.name')->extraAttributes(static function (Pet $record): array {
                    return ['style' => 'background-color:' . $record->pet_location->color, 'class' => 'table-text-column-badge'];
                })
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('species')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('chip'),
                Tables\Columns\TextColumn::make('birth_date')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('entry_date')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('sterilized')
                    ->boolean(),
                Tables\Columns\TextColumn::make('color'),
                Tables\Columns\TextColumn::make('coat'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                    ]),
                Tables\Filters\SelectFilter::make('sterilized')
                    ->options([
                        1 => 'Yes',
                        0 => 'No',
                    ]),
                Tables\Filters\SelectFilter::make('pet_statuses_id')
                    ->multiple()
                    ->options(PetStatus::all()->pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('pet_locations_id')
                    ->multiple()
                    ->options(PetLocation::all()->pluck('name', 'id')),
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
            //
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
    private static function getOptionWithColor(\Illuminate\Database\Eloquent\Collection $model)
    {
        return $model->mapWithKeys(function ($item) {
            return [$item['id'] => view('filament.components.select-with-color')
                ->with('name', $item['name'])
                ->with('color', $item['color'])
                ->render()];
        });
    }
    private static function getRadioWithColor(\Illuminate\Database\Eloquent\Collection $model)
    {
        return $model->mapWithKeys(function ($item) {
            return [
                $item['id'] => view('filament.components.select-with-color')
                    ->with('name', $item['name'])
                    ->with('color', $item['color'])
            ];
        });
    }
}
