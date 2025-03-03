<?php

namespace App\Filament\Resources\Definitions;

use App\Filament\Resources\Definitions\VaccineResource\Pages;
use App\Filament\Resources\Definitions\VaccineResource\RelationManagers;
use App\Models\PetHasVaccine;
use App\Models\Vaccine;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VaccineResource extends Resource
{
    protected static ?string $model = Vaccine::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $slug = 'definitions/vaccine';

    protected static ?string $recordTitleAttribute = 'name';


    public static function getNavigationGroup(): ?string
    {
        return __('Definitions');
    }

    public static function getNavigationLabel(): string
    {
        return __('Vaccines');
    }
    public static function getModelLabel(): string
    {
        return __('vaccine');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->translateLabel()
                    ->required()
                    ->maxLength(100)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('expire')
                    ->translateLabel()
                    ->numeric()
                    ->suffix('days')
                    ->helperText(__('Number of days to be renewed. Leave blank if don\'t need to be renewed')),
                Forms\Components\TextInput::make('notification')
                    ->translateLabel()
                    ->numeric()
                    ->suffix('days')
                    ->helperText(__('Number of day before/after to send notification to be rescheduled')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('expire')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('notification')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('created_at')
                    ->translateLabel()
                    ->dateTime(config('filament.date_time_format'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->translateLabel()
                    ->dateTime(config('filament.date_time_format'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                ->before(function (Tables\Actions\DeleteAction $action,Vaccine $record) {
                    if(PetHasVaccine::where('vaccine_id',$record->id)->exists()) {
                        $action->cancel();
                        Notification::make('cant_delete_record')
                        ->title(__('Operation canceled. There is data associated with this record'))
                        ->danger()
                        ->send();
                    }
                }),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->groupedBulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
                // Tables\Actions\ForceDeleteBulkAction::make(),
                // Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageVaccines::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
