<?php

namespace App\Filament\Resources\Definitions\StatusResource\Pages;

use App\Filament\Resources\Definitions\StatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageStatus extends ManageRecords
{
    protected static string $resource = StatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
