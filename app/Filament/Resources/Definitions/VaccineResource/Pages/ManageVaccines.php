<?php

namespace App\Filament\Resources\Definitions\VaccineResource\Pages;

use App\Filament\Resources\Definitions\VaccineResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageVaccines extends ManageRecords
{
    protected static string $resource = VaccineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
