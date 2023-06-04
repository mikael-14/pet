<?php

namespace App\Filament\Resources\PersonResource\Pages;

use App\Filament\Resources\PersonResource;
use App\Models\Person;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPerson extends ViewRecord
{
    protected static string $resource = PersonResource::class;
 
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['flags'] = Person::find($this->record->id)->person_flags()->pluck('name')->toArray();
        return $data;
    }

}
