<?php

namespace App\Filament\Resources\Definitions\DietResource\Pages;

use App\Filament\Resources\Definitions\DietResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDiets extends ManageRecords
{
    protected static string $resource = DietResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
