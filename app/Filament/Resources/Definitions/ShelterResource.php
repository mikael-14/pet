<?php

namespace App\Filament\Resources\Definitions;

use App\Filament\Resources\Definitions\ShelterResource\Pages;
use App\Filament\Resources\Definitions\ShelterResource\Pages\ViewShelter;
use App\Filament\Resources\Definitions\ShelterResource\RelationManagers;
use App\Models\Shelter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Cheesegrits\FilamentGoogleMaps\Fields\Geocomplete;
use Closure;
use Filament\Facades\Filament;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Set;
use Filament\Forms\Get;

class ShelterResource extends Resource
{
    protected static ?string $model = Shelter::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $slug = 'definitions/shelters';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Definitions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100)
                            ->columnSpan(9),
                        Forms\Components\Toggle::make('status')
                            ->inline(false)
                            ->default(1)
                            ->required()
                            ->columnSpan(1),
                    ])->columns(10),
                Forms\Components\Section::make('Address')
                    ->schema([
                        Forms\Components\TextInput::make('street')
                            ->maxLength(100)
                            ->suffixActions([
                                Forms\Components\Actions\Action::make('map')
                                    ->icon('tabler-map-search')
                                    ->label('Map')
                                    ->hiddenLabel()
                                    ->action(function (Get $get, Set $set) {
                                        $set('show_geocomplete', false);
                                        $set('show_map', !$get('show_map'));
                                    }),
                                Forms\Components\Actions\Action::make('geolocate')
                                    ->icon('tabler-input-search')
                                    ->label('Geocode')
                                    ->hiddenLabel()
                                    ->action(function (Get $get, Set $set) {
                                        $set('show_geocomplete', !$get('show_geocomplete'));
                                        $set('show_map', false);
                                    })
                            ])
                            ->columnSpan(10),
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
                            ->visible(fn (\Filament\Forms\Get $get): bool => $get('show_geocomplete'))
                            ->hint('Search by Google')
                            ->helperText('Search an address to help get data')
                            ->hiddenOn('view')
                            ->columnSpanFull(),
                        Map::make('map')
                            ->mapControls([
                                'mapTypeControl'    => true,
                                'scaleControl'      => true,
                                'streetViewControl' => true,
                                'rotateControl'     => false,
                                'fullscreenControl' => false,
                                'searchBoxControl'  => false, // creates geocomplete field inside map
                                'zoomControl'       => false,
                            ])
                            ->defaultLocation([41.2378425, -8.6200656])
                            ->defaultZoom(14)
                            ->reverseGeocode([
                                'country' => '%c',
                                'state' => '%A1',
                                'local' => '%L',
                                'street' => '%S %n',
                                'zip' => '%z',
                            ])
                            ->draggable() // allow dragging to move marker
                            ->clickable() // allow clicking to move marker
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $set('latitude', $state['lat']);
                                $set('longitude', $state['lng']);
                            })
                            ->hint('Map by Google')
                            ->helperText('Move the pin to help get data')
                            ->visible(fn (\Filament\Forms\Get $get, $livewire): bool => $livewire instanceof ViewRecord && $get('latitude') && $get('longitude') ? true : $get('show_map'))
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('show_geocomplete')->reactive()->default(false)->dehydrated(false)->hidden(),
                        Forms\Components\Toggle::make('show_map')->reactive()->default(false)->dehydrated(false)->hidden(),
                        Forms\Components\TextInput::make('latitude')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $set('map', [
                                    'lat' => floatVal($state),
                                    'lng' => floatVal($get('longitude')),
                                ]);
                                $set('location', [
                                    'lat' => floatVal($state),
                                    'lng' => floatVal($get('longitude')),
                                ]);
                            })
                            ->hidden()
                            ->lazy(), // important to use lazy, to avoid updates as you type
                        Forms\Components\TextInput::make('longitude')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $set('map', [
                                    'lat' => floatval($get('latitude')),
                                    'lng' => floatVal($state),
                                ]);
                                $set('location', [
                                    'lat' => floatval($get('latitude')),
                                    'lng' => floatVal($state),
                                ]);
                            })
                            ->hidden()
                            ->lazy(),
                        Forms\Components\Select::make('country')
                            ->searchable(true)
                            ->native(false)
                            ->options(__('pet/country'))
                            ->columnSpan(5),
                        Forms\Components\TextInput::make('state')
                            ->maxLength(100)
                            ->columnSpan(5),
                        Forms\Components\TextInput::make('local')
                            ->maxLength(100)
                            ->columnSpan(5),
                        Forms\Components\TextInput::make('zip')
                            ->placeholder('9999-999')
                            ->mask('9999-999')
                            ->maxLength(20)
                            ->columnSpan(5),

                    ])->columns(10)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('country')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('state')->searchable(),
                Tables\Columns\TextColumn::make('local'),
                Tables\Columns\TextColumn::make('street'),
                Tables\Columns\TextColumn::make('zip')->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('map')
                    ->label('View in map')
                    ->color('info')
                    ->url(fn (Shelter $record) => "https://www.google.com/maps?q=$record->latitude,$record->longitude")
                    ->visible(fn (Shelter $record): bool => !empty($record->latitude) && !empty($record->longitude) ? true : false)
                    ->openUrlInNewTab()
                    ->icon('tabler-map-2'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ShelterBlocksRelationManager::class,
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
