<?php

namespace App\Filament\Resources\Definitions;

use App\Filament\Resources\Definitions\EntryStatusResource\Pages;
use App\Filament\Resources\Definitions\EntryStatusResource\RelationManagers;
use App\Models\EntryStatus;
use App\Models\Pet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EntryStatusResource extends Resource
{
    protected static ?string $model = EntryStatus::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $slug = 'definitions/entry-status';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Definitions';


    public static function getNavigationLabel(): string
    {
        return __('Entry Statuses');
    }
    public static function getModelLabel(): string
    {
        return __('entry status');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->translateLabel()
                    ->required()
                    ->maxLength(20),
                Forms\Components\ColorPicker::make('color')
                ->translateLabel(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->translateLabel(),
                Tables\Columns\ColorColumn::make('color')->translateLabel(),
                Tables\Columns\TextColumn::make('created_at')
                    ->translateLabel()
                    ->dateTime(config('filament.date_time_format')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                ->before(function (Tables\Actions\DeleteAction $action,EntryStatus $record) {
                    if(Pet::where('entry_status_id',$record->id)->exists()) {
                        $action->cancel();
                        Notification::make('cant_delete_record')
                        ->title(__('Operation canceled. There is data associated with this record'))
                        ->danger()
                        ->send();
                    }
                }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageEntryStatus::route('/'),
        ];
    }
}
