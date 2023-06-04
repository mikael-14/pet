<?php

namespace App\Filament\Resources\Definitions\ClinicResource\Pages;

use App\Filament\Resources\Definitions\ClinicResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClinic extends EditRecord
{
    protected static string $resource = ClinicResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
