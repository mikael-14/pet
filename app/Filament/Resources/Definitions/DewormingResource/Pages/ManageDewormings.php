<?php

namespace App\Filament\Resources\Definitions\DewormingResource\Pages;

use App\Filament\Resources\Definitions\DewormingResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDewormings extends ManageRecords
{
    protected static string $resource = DewormingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
