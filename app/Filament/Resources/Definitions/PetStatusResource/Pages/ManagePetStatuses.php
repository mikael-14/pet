<?php

namespace App\Filament\Resources\Definitions\PetStatusResource\Pages;

use App\Filament\Resources\Definitions\PetStatusResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePetStatuses extends ManageRecords
{
    protected static string $resource = PetStatusResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
