<?php

namespace App\Filament\Resources\PersonResource\Pages;

use App\Filament\Resources\PersonResource;
use App\Models\PersonFlag;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePerson extends CreateRecord
{
    protected static string $resource = PersonResource::class;

    protected function afterCreate(): void
    {
        // Runs after the form fields are saved to the database.
        $id = $this->record->id;
        $state = $this->form->getState();
        //lets insert
        foreach ($state['flags']  as $flag) {
            PersonFlag::insert([
                'person_id' => $id,
                'name' => $flag
            ]);
        }
    }
}
