<?php

namespace App\Filament\Resources\Definitions;

use App\Filament\Resources\Definitions\TestResource\Pages;
use App\Filament\Resources\Definitions\TestResource\RelationManagers;
use App\Models\PetHasTest;
use App\Models\Test;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TestResource extends Resource
{
    protected static ?string $model = Test::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $slug = 'definitions/test';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Definitions';

    public static function getNavigationLabel(): string
    {
        return __('Tests');
    }
    public static function getModelLabel(): string
    {
        return __('test');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->translateLabel()
                    ->columnSpanFull()
                    ->required()
                    ->maxLength(100),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->translateLabel(),
                Tables\Columns\TextColumn::make('created_at')->translateLabel()
                    ->dateTime(config('filament.date_time_format'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('deleted_at')->translateLabel()
                    ->dateTime(config('filament.date_time_format'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                ->before(function (Tables\Actions\DeleteAction $action,Test $record) {
                    if(PetHasTest::where('test_id',$record->id)->exists()) {
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
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
                // Tables\Actions\ForceDeleteBulkAction::make(),
                // Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTests::route('/'),
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
