<?php

namespace App\Filament\Resources\ClinicResource\RelationManagers;

use App\Filament\Resources\ClinicResource\Pages\ViewClinic;
use App\Filament\Resources\PersonResource;
use App\Models\Person;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PeopleRelationManager extends RelationManager
{
    protected static string $relationship = 'people';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return PersonResource::form($form);
    }

    public static function getTitle($ownerRecord, $pageClass): string
    {
        return ucfirst(__('person'));
    }

    public static function getModelLabel(): string
    {
        return __('person');
    }

    
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->translateLabel()->searchable(),
                Tables\Columns\TextColumn::make('email')->translateLabel()->searchable(),
                Tables\Columns\TextColumn::make('flags')
                    ->translateLabel()
                    ->badge()
                    ->toggleable(),
            ])
            ->filters([
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->color('gray')
                ->visible(fn ($livewire) => $livewire->pageClass !== ViewClinic::class),
                Tables\Actions\AttachAction::make()->color('primary')
                ->visible(fn ($livewire) => $livewire->pageClass !== ViewClinic::class),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                ->visible(fn ($livewire) => $livewire->pageClass !== ViewClinic::class),
                Tables\Actions\DetachAction::make()
                ->visible(fn ($livewire) => $livewire->pageClass !== ViewClinic::class),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
            ]);
    }
}
