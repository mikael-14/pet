<?php

namespace App\Filament\Resources\Definitions\PetLocationResource\Pages;

use App\Filament\Resources\Definitions\PetLocationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePetLocations extends ManageRecords
{
    protected static string $resource = PetLocationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
