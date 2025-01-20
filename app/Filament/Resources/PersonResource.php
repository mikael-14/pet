<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonResource\Pages;
use App\Filament\Resources\PersonResource\RelationManagers;
use App\Models\Person;
use App\Models\PersonFlag;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Cheesegrits\FilamentGoogleMaps\Fields\Geocomplete;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;

class PersonResource extends Resource implements HasShieldPermissions
{

    protected static ?string $model = Person::class;

    protected static ?string $navigationIcon = 'tabler-user';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('People');
    }
    public static function getModelLabel(): string
    {
        return __('person');
    }

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
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->translateLabel()
                            ->required()
                            ->maxLength(200),
                        Forms\Components\Select::make('gender')
                            ->translateLabel()
                            ->options([
                                'undefined' => __('Undefined'),
                                'male' => __('Male'),
                                'female' => __('Female'),
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->translateLabel()
                            ->email()
                            ->required()
                            ->unique(table: Person::class, column: 'email', ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->translateLabel()
                            ->tel()
                            ->required()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('vat')
                            ->translateLabel()
                            ->maxLength(20)
                            ->unique(table: Person::class, column: 'vat',  ignoreRecord: true),
                        Forms\Components\TextInput::make('cc')
                            ->translateLabel()
                            ->maxLength(30),
                        Forms\Components\DatePicker::make('birth_date')->translateLabel()
                            ->native(false)
                            ->displayFormat(config('filament.date_format')),
                        Forms\Components\Select::make('user_id')->options(
                            Person::avaibleUsers()
                        )->searchable()->translateLabel()
                            ->visible(Filament::auth()->user()->can('set_user_person'))
                            ->placeholder('Select to set user')
                            ->label(ucfirst(__('user'))),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Textarea::make('observation')->translateLabel()
                                    ->maxLength(65535),
                                Forms\Components\CheckboxList::make('flags')->translateLabel()
                                    ->options(PersonFlag::flags())
                            ])
                    ])->columns(2),
                Forms\Components\Section::make('Address')
                    ->heading(__('Address'))
                    ->schema([
                        Forms\Components\TextInput::make('street')
                            ->maxLength(100)
                            ->translateLabel()
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
                            ->placeholder(__('Search') . ' ...')
                            ->visible(fn (\Filament\Forms\Get $get): bool => $get('show_geocomplete'))
                            ->hint(__('Search by Google'))
                            ->helperText(__('Search an address to help get data'))
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
                            ->hint(__('Map by Google'))
                            ->helperText(__('Move the pin to help get data'))
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
                            ->translateLabel()
                            ->options(__('pet/country'))
                            ->columnSpan(5),
                        Forms\Components\TextInput::make('state')
                            ->translateLabel()
                            ->maxLength(100)
                            ->columnSpan(5),
                        Forms\Components\TextInput::make('local')
                            ->translateLabel()
                            ->maxLength(100)
                            ->columnSpan(5),
                        Forms\Components\TextInput::make('zip')
                            ->translateLabel()
                            ->placeholder('9999-999')
                            ->mask('9999-999')
                            ->maxLength(20)
                            ->columnSpan(5),
                    ])->columns(10),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->translateLabel()->searchable(),
                Tables\Columns\TextColumn::make('email')->translateLabel()->searchable(),
                Tables\Columns\TextColumn::make('phone')->translateLabel()->searchable(),
                Tables\Columns\TextColumn::make('vat')->translateLabel()->searchable(),
                Tables\Columns\TextColumn::make('cc')->translateLabel()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('flags')->translateLabel()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Black list' => 'danger',
                        'Adopter' => 'info',
                        'Temporary host family' => 'warning',
                        'Sponsor' => 'info',
                        'Veterinary' => 'success',
                        default => 'primary',
                    })
                    ->getStateUsing(function ($record) {
                        return $record->person_flags()->get()->map(function ($item) {
                            return $item->getName();
                        })->toArray();
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('birth_date')->translateLabel()
                    ->date(config('filament.date_format'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->translateLabel()
                    ->dateTime(config('filament.date_time_format'))->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')->translateLabel()
                    ->dateTime(config('filament.date_time_format'))->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('flags')
                    ->multiple()
                    ->options([
                        'adopter' => 'adopter',
                        'black_list' => 'black_list',
                        'cleaning_volunteer' => 'cleaning_volunteer',
                        'driver_volunteer' => 'driver_volunteer',
                        'medication_volunteer' => 'medication_volunteer',
                        'temporary_family' => 'temporary_family',
                        'sponsor' => 'sponsor',
                        'veterinary' => 'veterinary',
                    ])
                    ->query(
                        fn (Builder $query, array $data): Builder =>
                        $query->join('person_flags', 'person_flags.person_id', '=', 'people.id')
                    ),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->groupedBulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ClinicsRelationManager::class,
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
        return ['name', 'email'];
    }
}
