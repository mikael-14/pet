<?php

namespace App\Filament\Resources\PetResource\Pages;

use App\Filament\Resources\PetResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPet extends ViewRecord
{
    protected static string $resource = PetResource::class;
    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
            Actions\Action::make('back')
            ->icon('heroicon-o-arrow-left')
            ->color('secondary')
            ->action(function () {
                redirect($this->getResource()::getUrl('index'));
        }),
        ];
    }
    // to customie return view just fill this $view  variable with the path of your blade
    //protected static string $view = 'filament.pages.pet.view-record';

}
