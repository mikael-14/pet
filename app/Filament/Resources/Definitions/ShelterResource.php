<?php

namespace App\Filament\Resources\Definitions;

use App\Filament\Resources\Definitions\ShelterResource\Pages;
use App\Filament\Resources\Definitions\ShelterResource\RelationManagers;
use App\Models\Shelter;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Cheesegrits\FilamentGoogleMaps\Fields\Geocomplete;
use Filament\Forms\Components\Tabs;

class ShelterResource extends Resource
{
    protected static ?string $model = Shelter::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $slug = 'definitions/shelters';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Definitions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Card::make()
                ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(100)
                    ->columnSpanFull(),
                Tabs::make('Search Address')->tabs([
                    Tabs\Tab::make('Typing')
                        ->icon('tabler-input-search')
                        ->schema([
                            Geocomplete::make('location')
                                ->isLocation()
                                ->reverseGeocode([
                                    'country' => '%c',
                                    'state' => '%A1',
                                    'local' => '%L',
                                    'street' => '%S %n',
                                    'zip' => '%z',
                                ]) // reverse geocode marker location to form fields, see notes below
                                ->countries(['pt']) // restrict autocomplete results to these countries
                                ->updateLatLng() // update the lat/lng fields on your form when a Place is selected
                                ->maxLength(1024)
                                ->placeholder('Search ...')
                                ->hiddenOn('view')
                                ->hint('Search by Google')
                                ->helperText('Search an address to help get data')
                                ->columnSpanFull(),
                        ])
                        ->hiddenOn('view'),
                    Tabs\Tab::make('Marker')
                        ->icon('tabler-map-search')
                        ->schema([
                            Map::make('map')
                                ->mapControls([
                                    'mapTypeControl'    => false,
                                    'scaleControl'      => false,
                                    'streetViewControl' => false,
                                    'rotateControl'     => false,
                                    'fullscreenControl' => false,
                                    'searchBoxControl'  => false, // creates geocomplete field inside map
                                    'zoomControl'       => false,
                                ])
                                ->reactive()
                                ->reverseGeocode([
                                    'country' => '%c',
                                    'state' => '%A1',
                                    'local' => '%L',
                                    'street' => '%S %n',
                                    'zip' => '%z',
                                ])
                                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                    $set('latitude', $state['lat']);
                                    $set('longitude', $state['lng']);
                                })
                                ->hint('Map by Google')
                                ->helperText('Move the pin to help get data')
                                ->columnSpanFull(),
                        ]),
                ])->columnSpanFull(),


                Forms\Components\TextInput::make('latitude')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $set('location', [
                            'lat' => floatVal($state),
                            'lng' => floatVal($get('longitude')),
                        ]);
                        $set('map', [
                            'lat' => floatVal($state),
                            'lng' => floatVal($get('longitude')),
                        ]);
                    })
                    ->visible(false)
                    ->lazy(), // important to use lazy, to avoid updates as you type
                Forms\Components\TextInput::make('longitude')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $set('location', [
                            'lat' => floatval($get('latitude')),
                            'lng' => floatVal($state),
                        ]);
                        $set('map', [
                            'lat' => floatval($get('latitude')),
                            'lng' => floatVal($state),
                        ]);
                    })
                    ->visible(false)
                    ->lazy(),
                Forms\Components\Select::make('country')
                    ->options(config('pet-country')),
                Forms\Components\TextInput::make('state')
                    ->maxLength(100),
                Forms\Components\TextInput::make('local')
                    ->maxLength(100),
                Forms\Components\TextInput::make('street')
                    ->maxLength(100),
                Forms\Components\TextInput::make('zip')
                    ->maxLength(20),
                Forms\Components\Toggle::make('status')
                    ->inline(false)
                    ->helperText('Active')
                    ->default(1)
                    ->required(),
            ])->columns(2)
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('country'),
                Tables\Columns\TextColumn::make('state'),
                Tables\Columns\TextColumn::make('local'),
                Tables\Columns\TextColumn::make('street'),
                Tables\Columns\TextColumn::make('zip'),
                Tables\Columns\TextColumn::make('latitude'),
                Tables\Columns\TextColumn::make('longitude'),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ShelterLocationsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShelters::route('/'),
            'create' => Pages\CreateShelter::route('/create'),
            'edit' => Pages\EditShelter::route('/{record}/edit'),
            'view' => Pages\ViewShelter::route('/{record}'),
        ];
    }
}
