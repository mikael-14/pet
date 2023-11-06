<?php

namespace App\Filament\Resources\Definitions\ShelterResource\Pages;

use App\Filament\Resources\Definitions\ShelterResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewShelter extends ViewRecord
{
    protected static string $resource = ShelterResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
