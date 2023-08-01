<?php

namespace App\Filament\Resources\PersonResource\RelationManagers;

use App\Filament\Resources\ClinicResource;
use App\Filament\Resources\PersonResource\Pages\ViewPerson;
use App\Models\Clinic;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClinicsRelationManager extends RelationManager
{
    protected static string $relationship = 'clinics';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return ClinicResource::form($form);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('state')->searchable(),
                Tables\Columns\TextColumn::make('local'),
                Tables\Columns\TextColumn::make('street'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->color('secondary')
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
