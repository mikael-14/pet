<?php

namespace App\Filament\Resources\Definitions\ShelterLocationResource\Pages;

use App\Filament\Resources\Definitions\ShelterLocationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageShelterLocations extends ManageRecords
{
    protected static string $resource = ShelterLocationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
