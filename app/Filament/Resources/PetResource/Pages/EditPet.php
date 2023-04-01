<?php

namespace App\Filament\Resources\PetResource\Pages;

use App\Filament\Resources\PetResource;
use App\Models\Pet;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\PetLocation;
use App\Models\PetStatus;
use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class EditPet extends EditRecord
{
    protected static string $resource = PetResource::class;
    public function getFormSchema(): array
    {
        return [
            Group::make()
                ->schema([
                    Group::make()
                        ->schema([
                            Card::make()
                                ->schema([
                                    TextInput::make('name')
                                        ->required()
                                        ->maxLength(255),
                                    Select::make('species')
                                        ->options(
                                            config('pet-species')
                                        )->default(array_key_first(config('pet-species')))
                                        ->disablePlaceholderSelection(),
                                    Select::make('gender')
                                        ->options([
                                            'male' => 'Male',
                                            'female' => 'Female',
                                        ])->required(),
                                    DatePicker::make('birth_date')
                                        ->displayFormat(config('filament.date_format')),
                                    TextInput::make('chip')
                                        ->maxLength(20)
                                        ->hint(fn ($state) => 'Digits: ' . strlen($state) . '')
                                        ->lazy(),
                                    DatePicker::make('chip_date')
                                        ->displayFormat(config('filament.date_format')),
                                    TextInput::make('color')
                                        ->maxLength(50),
                                    TextInput::make('coat')
                                        ->maxLength(50),
                                    RichEditor::make('observation')->columnSpan('full'),
                                ])->columns(2),
                            Section::make('Status')
                                ->schema([
                                    Select::make('pet_statuses_id')
                                        ->allowHtml()
                                        ->searchable()
                                        ->preload()
                                        ->options(
                                            PetResource::getOptionWithColor(PetStatus::all())
                                        )->required(),
                                    Select::make('pet_locations_id')
                                        ->allowHtml()
                                        ->searchable()
                                        ->preload()
                                        ->options(
                                            PetResource::getOptionWithColor(PetLocation::all())
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
                                ])->collapsible()->columns(2),
                        ])->columnSpan(['lg' => 2]),

                    Group::make()
                        ->schema([
                            Section::make('Image')
                                ->schema([
                                    SpatieMediaLibraryFileUpload::make('image')
                                        ->acceptedFileTypes(['image/*'])
                                        ->disk('petsMainImage')
                                        ->collection('pets-main-image')
                                        ->enableOpen()
                                        ->enableDownload()
                                        ->columnSpan('full'),
                                ])->collapsible(),

                        ])->columnSpan(['lg' => 1]),

                ])
                ->columns(3)
        ];
    }
    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
            //Actions\ForceDeleteAction::make(),
        ];
    }
}
