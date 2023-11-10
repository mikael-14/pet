<?php

namespace App\Filament\Resources\Definitions\ShelterResource\Pages;

use App\Filament\Resources\Definitions\ShelterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShelter extends EditRecord
{
    protected static string $resource = ShelterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
