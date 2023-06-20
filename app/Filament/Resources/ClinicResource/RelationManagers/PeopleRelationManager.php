<?php

namespace App\Filament\Resources\ClinicResource\RelationManagers;

use App\Filament\Resources\PersonResource;
use App\Models\Person;
use Awcodes\FilamentBadgeableColumn\Components\BadgeableTagsColumn;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PeopleRelationManager extends RelationManager
{
    protected static string $relationship = 'people';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return PersonResource::form($form);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                BadgeableTagsColumn::make('flags')
                ->colors([
                    'danger' => 'Black list',
                    '#fdecce' => 'Adopter',
                    '#fceacc' => 'Temporary host family',
                    '#f7e7cd' => 'Sponsor',
                    '#f7e3c3' => 'Veterinary',
                ])
                ->getStateUsing(function ($record) {
                    return $record->person_flags()->get()->map(function ($item){
                        return $item->getName();
                    })->toArray();
                })
                ->searchable()
                ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->color('secondary'),
                Tables\Actions\AttachAction::make()->color('primary'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
            ]);
    }
}
