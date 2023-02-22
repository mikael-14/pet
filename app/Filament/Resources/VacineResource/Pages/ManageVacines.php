<?php

namespace App\Filament\Resources\VacineResource\Pages;

use App\Filament\Resources\VacineResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageVacines extends ManageRecords
{
    protected static string $resource = VacineResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
