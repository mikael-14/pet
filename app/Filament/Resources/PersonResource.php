<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonResource\Pages;
use App\Filament\Resources\PersonResource\RelationManagers;
use App\Models\Person;
use App\Models\PersonFlag;
use App\Models\User;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Tabs;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Cheesegrits\FilamentGoogleMaps\Fields\Geocomplete;

class PersonResource extends Resource implements HasShieldPermissions
{

    protected static ?string $model = Person::class;

    protected static ?string $navigationIcon = 'tabler-user';

    protected static ?string $recordTitleAttribute = 'name';


    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'view_owned',
            'view_all',
            'set_user',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(200),
                        Forms\Components\Select::make('gender')
                            ->options([
                                'undefined' => 'Undefined',
                                'male' => 'Male',
                                'female' => 'Female',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(table: Person::class, column: 'email', ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->required()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('vat')
                            ->maxLength(20)
                            ->unique(table: Person::class, column: 'vat',  ignoreRecord: true),
                        Forms\Components\TextInput::make('cc')
                            ->maxLength(30),
                        Forms\Components\DatePicker::make('birth_date')
                        ->displayFormat(config('filament.date_format')),
                        Forms\Components\Select::make('users_id')->options(
                            Person::avaibleUsers()
                        )->searchable()->visible(Filament::auth()->user()->can('set_user_person')),
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
                                        ->debug()
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
                            ->visible(true)
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
                            ->visible(true)
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
                        Forms\Components\Textarea::make('observation')
                            ->maxLength(65535),
                        Forms\Components\CheckboxList::make('flags')
                            ->options(PersonFlag::flags())
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('phone')->searchable(),
                Tables\Columns\TextColumn::make('vat')->searchable(),
                Tables\Columns\TextColumn::make('cc')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('birth_date')
                ->date(config('filament.date_format'))
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                ->dateTime(config('filament.date_time_format'))->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('deleted_at')
                ->dateTime(config('filament.date_time_format'))->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeople::route('/'),
            'create' => Pages\CreatePerson::route('/create'),
            'edit' => Pages\EditPerson::route('/{record}/edit'),
            'view' => Pages\ViewPerson::route('/{record}'),
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
        return ['name'];
    }
}
