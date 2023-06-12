<?php

namespace App\Filament\Resources\PersonResource\Pages;

use App\Filament\Resources\PersonResource;
use App\Models\Person;
use App\Models\PersonFlag;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPerson extends EditRecord
{
    protected static string $resource = PersonResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
    protected function afterSave(): void
    {
        // Runs after the form fields are saved to the database.
        $id = $this->record->id;
        $state = $this->form->getState();
        //let's delete all flags 
        PersonFlag::where('person_id', $id)->delete();
        //lets insert
        foreach ($state['flags']  as $flag) {
            PersonFlag::insert([
                'person_id' => $id,
                'name' => $flag
            ]);
        }
    }
}
