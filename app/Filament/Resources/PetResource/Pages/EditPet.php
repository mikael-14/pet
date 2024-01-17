<?php

namespace App\Filament\Resources\PetResource\Pages;

use App\Filament\Resources\PetResource;
use App\Models\EntryStatus;
use App\Models\Pet;
use App\Models\ShelterBlock;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Support\Enums\Alignment;
use Kenepa\ResourceLock\Resources\Pages\Concerns\UsesResourceLock;

class EditPet extends EditRecord
{
    use UsesResourceLock;
    protected static string $resource = PetResource::class;


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([
                    'sm' => 1,
                    'md' => 3,
                ])
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Section::make()
                                    ->schema([
                                        TextInput::make('name')
                                        ->translateLabel()
                                            ->required()
                                            ->maxLength(255),
                                        Select::make('species')
                                        ->translateLabel()
                                            ->options(
                                                __('pet/species')
                                            )->default(array_key_first(__('pet/species')))
                                            ->selectablePlaceholder(false),
                                        Select::make('gender')
                                        ->translateLabel()
                                            ->options([
                                                'male' => __('male'),
                                                'female' => __('female'),
                                            ])->required(),
                                        DatePicker::make('birth_date')
                                        ->translateLabel()
                                            ->native(false)
                                            ->displayFormat(config('filament.date_format')),
                                        TextInput::make('chip')
                                        ->translateLabel()
                                            ->unique(table: Pet::class, column: 'chip', ignorable: fn () => $this->getRecord(), ignoreRecord: true)
                                            ->maxLength(20)
                                            ->hint(fn ($state) => 'Digits: ' . strlen($state) . '')
                                            ->lazy(),
                                        DatePicker::make('chip_date')
                                        ->translateLabel()
                                            ->native(false)
                                            ->displayFormat(config('filament.date_format')),
                                        TextInput::make('color')->translateLabel()
                                            ->maxLength(50),
                                        TextInput::make('coat')->translateLabel()
                                            ->maxLength(50),
                                        TextInput::make('breed')->translateLabel()
                                            ->maxLength(50),
                                        Toggle::make('adoptable')->translateLabel()
                                            ->inline(false),
                                        RichEditor::make('observation')->translateLabel()->columnSpan('full'),
                                    ])->columns(2),
                                Section::make('Status')
                                    ->schema([
                                        Select::make('shelter_block_id')
                                        ->label(__('Shelter block'))
                                            ->allowHtml()
                                            ->searchable()
                                            ->preload()
                                            ->options(
                                                PetResource::getOptionWithColor(ShelterBlock::getOptions())
                                            )->required(),
                                        Select::make('entry_status_id')
                                        ->label(__('Entry status'))
                                            ->allowHtml()
                                            ->searchable()
                                            ->preload()
                                            ->options(
                                                PetResource::getOptionWithColor(EntryStatus::all())
                                            )->required(),
                                        DatePicker::make('entry_date')
                                        ->translateLabel()
                                            ->displayFormat(config('filament.date_format'))
                                            ->required(),

                                        Toggle::make('sterilized')->translateLabel()
                                            ->inline(false)->reactive(),
                                        DatePicker::make('sterilized_date')->translateLabel()
                                            ->native(false)
                                            ->displayFormat(config('filament.date_format'))
                                            ->visible(fn ($get) => $get('sterilized')),
                                        TextInput::make('sterilized_local')->translateLabel()
                                            ->visible(fn ($get) => $get('sterilized'))
                                            ->maxLength(50),
                                    ])->collapsible()->columns(2),
                            ])->columnSpan(2),
                        Grid::make(2)
                            ->schema([
                                Forms\Components\Section::make()
                                    ->schema([
                                        SpatieMediaLibraryFileUpload::make('image')
                                        ->translateLabel()
                                            ->acceptedFileTypes(['image/*'])
                                            ->disk('petsMainImage')
                                            ->visibility('private')
                                            ->collection('pets-main-image')
                                            ->openable()
                                            ->downloadable()
                                            ->deletable(false)
                                            ->hintAction(
                                                Forms\Components\Actions\Action::make('removeImage')
                                                ->label(__('Remove image'))
                                                    ->icon('heroicon-m-x-mark')
                                                    ->color('danger')
                                                    ->requiresConfirmation()
                                                    ->action(function (Forms\Set $set, $state) {
                                                        $set('image', null);
                                                    })
                                            )
                                            ->columnSpan('full'),

                                    ]),
                                Forms\Components\Section::make('Qrcode')
                                    ->schema([
                                        Hidden::make('qrcode')
                                            ->live(),
                                        Forms\Components\Actions::make([
                                            Forms\Components\Actions\Action::make('NewQrcode')
                                                ->label(__('New').' Qrcode')
                                                ->icon('tabler-new-section')
                                                ->color('info')
                                                ->requiresConfirmation()
                                                ->link()
                                                ->action(function (Set $set, Pet $record) {
                                                    $set('qrcode', md5(time() . $this->record->id));
                                                    $this->refreshFormData([
                                                        'Qrcodeview',
                                                    ]);
                                                }),
                                            Forms\Components\Actions\Action::make('RemoveQrcode')
                                                ->label(__('Remove').' Qrcode')
                                                ->icon('heroicon-m-x-mark')
                                                ->color('danger')
                                                ->requiresConfirmation()
                                                ->link()
                                                ->action(function (Set $set) {
                                                    $set('qrcode', null);
                                                    $this->refreshFormData([
                                                        'Qrcodeview',
                                                    ]);
                                                })
                                        ])->alignment(Alignment::Between),
                                        Placeholder::make('shout')
                                            ->label(false)
                                            ->content(function (\Filament\Forms\Get $get, Pet $record) {
                                                $qrcode = $record->qrcode ?: null;
                                                $text_empty = $qrcode ? null : 'No Qrcode' ;
                                                $text_save =null;
                                                if($qrcode !== $get('qrcode'))
                                                {
                                                    $text_save = (empty($get('qrcode'))) ? __('Removed') : __('Generated');
                                                    $text_save .= '. ';
                                                    $text_save .= __('Please save to persist the changes') ;
                                                } 
                                                return view('filament.components.qrcode')
                                                    ->with('qrcode', $qrcode)
                                                    ->with('text_save', $text_save)
                                                    ->with('text_empty', $text_empty);
                                            }),
                                    ])->collapsible(),
                                Forms\Components\Section::make()
                                    ->schema([
                                        Placeholder::make('Created')
                                            ->label(__('Created at'))
                                            ->content(fn (): ?string => $this->record?->created_at->diffForHumans()),
                                        Placeholder::make('updated_at')
                                            ->label(__('Updated at'))
                                            ->content(fn (): ?string => $this->record?->updated_at->diffForHumans()),
                                    ])
                            ])->columnSpan(1),
                    ]),
            ]);
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
            //Actions\ForceDeleteAction::make(),
        ];
    }
}
