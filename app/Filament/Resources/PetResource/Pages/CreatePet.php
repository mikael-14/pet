<?php

namespace App\Filament\Resources\PetResource\Pages;

use App\Filament\Resources\PetResource;
use App\Models\EntryStatus;
use App\Models\Pet;
use App\Models\ShelterBlock;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\CreateRecord;


class CreatePet extends CreateRecord
{
    protected static string $resource = PetResource::class;

    public function getFormSchema(): array
    {
        return [
            Forms\Components\Group::make()
                ->schema([
                    Forms\Components\Group::make()
                        ->schema([
                            Forms\Components\Section::make()
                                ->schema([
                                    TextInput::make('name')
                                        ->required()
                                        ->maxLength(255),
                                    Select::make('species')
                                        ->options(
                                            __('pet/species')
                                        )->default(array_key_first(__('pet/species')))
                                        ->selectablePlaceholder(false),
                                    Select::make('gender')
                                        ->options([
                                            'male' => 'Male',
                                            'female' => 'Female',
                                        ])->required(),
                                    DatePicker::make('birth_date')
                                        ->displayFormat(config('filament.date_format')),
                                    TextInput::make('chip')
                                        ->unique(table: Pet::class, column: 'chip', ignoreRecord: true)
                                        ->maxLength(20)
                                        ->hint(fn ($state) => 'Digits: ' . strlen($state) . '')
                                        ->lazy(),
                                    DatePicker::make('chip_date')
                                        ->displayFormat(config('filament.date_format')),
                                    TextInput::make('color')
                                        ->maxLength(50),
                                    TextInput::make('coat')
                                        ->maxLength(50),
                                    TextInput::make('breed')
                                        ->maxLength(50),
                                    Toggle::make('adoptable')
                                        ->inline(false),
                                    RichEditor::make('observation')->columnSpan('full'),
                                ])->columns(2),
                            Section::make('Status')
                                ->schema([
                                    Select::make('shelter_block_id')
                                        ->allowHtml()
                                        ->searchable()
                                        ->preload()
                                        ->options(
                                            PetResource::getOptionWithColor(ShelterBlock::getOptions())
                                        )->required(),
                                    Select::make('entry_status_id')
                                        ->allowHtml()
                                        ->searchable()
                                        ->preload()
                                        ->options(
                                            PetResource::getOptionWithColor(EntryStatus::all())
                                        )->required(),
                                    DatePicker::make('entry_date')
                                        ->displayFormat(config('filament.date_format'))
                                        ->required(),

                                    Toggle::make('sterilized')
                                        ->inline(false)->reactive(),
                                    DatePicker::make('sterilized_date')
                                        ->displayFormat(config('filament.date_format'))
                                        ->visible(fn ($get) => $get('sterilized')),
                                    TextInput::make('sterilized_local')
                                        ->visible(fn ($get) => $get('sterilized'))
                                        ->maxLength(50),
                                ])->columns(2)->collapsible(),
                        ])->columnSpan(['lg' => 2]),
                    Forms\Components\Group::make()
                        ->schema([
                            Forms\Components\Section::make()
                                ->schema([
                                    SpatieMediaLibraryFileUpload::make('image')
                                        ->acceptedFileTypes(['image/*'])
                                        ->disk('petsMainImage')
                                        ->collection('pets-main-image')
                                        ->openable()
                                        ->downloadable()
                                        ->columnSpan('full'),
                                ]),

                        ])->columnSpan(['lg' => 1]),
                ])
                ->columns(3)
        ];
    }
}
