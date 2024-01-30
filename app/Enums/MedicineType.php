<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
 
enum MedicineType: string implements HasLabel
{
    case pill = 'pill';
    case capsule = 'capsule';
    case liquid = 'liquid';
    case injection = 'injection';
    case toppical = 'toppical';
    case inhaler = 'inhaler';
    case suppository = 'suppository';
    case drop = 'drop';
    case powder = 'powder';

    public function getLabel(): ?string
    {
        return __("pet/medicine.{$this->name}");
    }

}