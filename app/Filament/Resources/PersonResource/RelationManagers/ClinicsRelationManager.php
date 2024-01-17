<?php

namespace App\Filament\Resources\PersonResource\RelationManagers;

use App\Filament\Resources\ClinicResource;
use App\Filament\Resources\PersonResource\Pages\ViewPerson;
use App\Models\Clinic;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClinicsRelationManager extends RelationManager
{
    protected static string $relationship = 'clinics';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return ClinicResource::form($form);
    }

    public static function getTitle($ownerRecord, $pageClass): string
    {
        return ucfirst(__('clinic'));
    }

    public static function getModelLabel(): string
    {
        return __('clinic');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->translateLabel()
                ->searchable(),
                Tables\Columns\TextColumn::make('state')->translateLabel()->searchable(),
                Tables\Columns\TextColumn::make('local')->translateLabel(),
                Tables\Columns\TextColumn::make('street')->translateLabel(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->color('gray')
                ->visible(fn ($livewire) => $livewire->pageClass !== ViewPerson::class),
                Tables\Actions\AttachAction::make()->color('primary')
                ->visible(fn ($livewire) => $livewire->pageClass !== ViewPerson::class),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                ->visible(fn ($livewire) => $livewire->pageClass !== ViewPerson::class),
                Tables\Actions\DetachAction::make()
                ->visible(fn ($livewire) => $livewire->pageClass !== ViewPerson::class),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
            ]);
    }    
}
