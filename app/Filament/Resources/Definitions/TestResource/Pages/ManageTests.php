<?php

namespace App\Filament\Resources\Definitions\TestResource\Pages;

use App\Filament\Resources\Definitions\TestResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTests extends ManageRecords
{
    protected static string $resource = TestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
