<?php

namespace App\Filament\Resources\Definitions\ShelterResource\Pages;

use App\Filament\Resources\Definitions\ShelterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShelters extends ListRecords
{
    protected static string $resource = ShelterResource::class; 

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
