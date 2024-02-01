<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
 
enum PetMeasure: string implements HasLabel
{
    case Weight = 'Weight';
    
    public function getLabel(): ?string
    {
        return __("pet/measures.{$this->name}");
    }


}