<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
 
enum PersonFlag: string implements HasLabel, HasColor
{
    case CLEANING_VOLUNTEER = 'cleaning_volunteer';
    case DRIVER_VOLUNTEER = 'driver_volunteer';
    case MEDICATION_VOLUNTEER =  'medication_volunteer';
    case TEAMPORAY_FAMILY = 'temporary_family';
    case VETERINARY = 'veterinary';
    case ADOPTER = 'adopter';
    case SPONSOR = 'sponsor';
    case BLACK_LIST = 'black_list';
    
    public function getLabel(): ?string
    {
        return match ($this) {
            self::CLEANING_VOLUNTEER => __('pet/personflag.cleaning_volunteer'),
            self::DRIVER_VOLUNTEER => __('pet/personflag.driver_volunteer'),
            self::MEDICATION_VOLUNTEER => __('pet/personflag.medication_volunteer'),
            self::TEAMPORAY_FAMILY => __('pet/personflag.temporary_family'),
            self::VETERINARY => __('pet/personflag.veterinary'),
            self::ADOPTER => __('pet/personflag.adopter'),
            self::SPONSOR => __('pet/personflag.sponsor'),
            self::BLACK_LIST => __('pet/personflag.black_list'),
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::TEAMPORAY_FAMILY => 'warning',
            self::SPONSOR => 'info',
            self::ADOPTER => 'info',
            self::BLACK_LIST => 'danger',
            self::VETERINARY => 'success',
            default => 'primary',
        };
    }
}