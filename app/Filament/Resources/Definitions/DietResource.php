<?php

namespace  App\Filament\Resources\Definitions;

use App\Filament\Resources\Definitions\DietResource\Pages;
use App\Filament\Resources\Definitions\DietResource\RelationManagers;
use App\Models\Diet;
use App\Models\PetHasDiet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DietResource extends Resource
{
    protected static ?string $model = Diet::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $slug = 'definitions/diet';

    protected static ?string $recordTitleAttribute = 'name';

 
    public static function getNavigationGroup(): ?string
    {
        return __('Definitions');
    }


    public static function getNavigationLabel(): string
    {
        return __('diets');
    }
    public static function getModelLabel(): string
    {
        return __('diet');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->translateLabel()
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(100),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->translateLabel(),
                Tables\Columns\TextColumn::make('created_at')->translateLabel()
                    ->dateTime(config('filament.date_time_format')),
                Tables\Columns\TextColumn::make('updated_at')->translateLabel()
                    ->dateTime(config('filament.date_time_format')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                ->before(function (Tables\Actions\DeleteAction $action,Diet $record) {
                    if(PetHasDiet::where('diet_id',$record->id)->exists()) {
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
            'index' => Pages\ManageDiets::route('/'),
        ];
    }
}
