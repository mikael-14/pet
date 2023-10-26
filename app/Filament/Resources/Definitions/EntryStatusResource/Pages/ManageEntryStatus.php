<?php

namespace App\Filament\Resources\Definitions\EntryStatusResource\Pages;

use App\Filament\Resources\Definitions\EntryStatusResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageEntryStatus extends ManageRecords
{
    protected static string $resource = EntryStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
