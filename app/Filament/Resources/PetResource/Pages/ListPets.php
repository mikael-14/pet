<?php

namespace App\Filament\Resources\PetResource\Pages;

use App\Filament\Resources\PetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Action;

class ListPets extends ListRecords
{
    protected static string $resource = PetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    protected function getTableEmptyStateIcon(): ?string 
    {
        return 'tabler-paw';
    }
 
    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No pets yet';
    }
 
    protected function getTableEmptyStateDescription(): ?string
    {
        return 'You may create a pet using the button below.';
    }
 
    protected function getTableEmptyStateActions(): array
    {
        return [
           Action::make('create')
                ->label('Create pet')
                ->url($this->getResource()::getUrl('create'))
                ->icon('heroicon-o-plus')
                ->button(),
        ];
    } 
}
