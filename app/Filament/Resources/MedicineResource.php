<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MedicineResource\Pages;
use App\Filament\Resources\MedicineResource\RelationManagers;
use App\Models\Medicine;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Lang;

class MedicineResource extends Resource
{
    protected static ?string $model = Medicine::class;

    protected static ?string $navigationIcon = 'tabler-pill';

    protected static ?string $slug = 'medication/medicines';
    
    public static function getNavigationGroup(): ?string
    {
        return __('Medications');
    }

    public static function getNavigationLabel(): string
    {
        return __('medicines');
    }
    public static function getModelLabel(): string
    {
        return __('medicine');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->translateLabel()
                    ->required()
                    ->maxLength(100),
                Forms\Components\Select::make('type')
                    ->translateLabel()
                    ->options(__('pet/medicine'))
                    ->required(),
                Forms\Components\TextInput::make('dosage')
                    ->translateLabel()
                    ->maxLength(50),
                Forms\Components\TextInput::make('application')
                    ->translateLabel()
                    ->maxLength(50),
                Forms\Components\Textarea::make('description')
                    ->translateLabel()
                    ->maxLength(500)
                    ->columnSpanFull(),
                Forms\Components\TagsInput::make('active_ingredient')
                    ->translateLabel()
                    ->placeholder(__('New active'))
                    // ->keyLabel('Property name')
                    // ->valueLabel('Property value')
                    // ->schema([
                    //     Forms\Components\TextInput::make('active_ingredient')->required(),
                    // ])
                    ->columnSpanFull(),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->translateLabel()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')->translateLabel()
                    ->formatStateUsing(fn (string $state): string =>  __('pet/medicine')[$state] ?? '-'),
                Tables\Columns\TextColumn::make('dosage')->translateLabel(),
                Tables\Columns\TextColumn::make('active_ingredient')->translateLabel()
                    ->badge()
                    ->color('primary')
                    ->searchable(),
                Tables\Columns\TextColumn::make('application')->translateLabel(),
                Tables\Columns\TextColumn::make('created_at')->translateLabel()
                    ->dateTime(config('filament.date_time_format'))->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')->translateLabel()
                    ->dateTime(config('filament.date_time_format'))->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('active_ingredient')->translateLabel()
                    ->multiple()
                    ->options(Medicine::getAllActiveIngredientFormatted())
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->whereJsonContains('active_ingredient', $data['values']);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->groupedBulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageMedicines::route('/'),
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
