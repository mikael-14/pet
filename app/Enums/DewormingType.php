<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
 
enum DewormingType: string implements HasLabel
{
    case INTERNAL = 'internal';
    case EXTERNAL = 'external';
    case INTERNALANDEXTERNAL = 'internal and external';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::INTERNAL => __('pet/deworming.internal'),
            self::EXTERNAL => __('pet/deworming.external'),
            self::INTERNALANDEXTERNAL => __('pet/deworming.internal_and_external'),
        };
    }
}