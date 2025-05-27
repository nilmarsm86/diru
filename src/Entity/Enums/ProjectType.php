<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;
use BackedEnum;

enum ProjectType: string
{
    use EnumsTrait;

    case Null = '';
    case Parcel = '0';
    case City = '1';

    const CHOICES = [self::Parcel, self::City];

    /**
     * @param BackedEnum|string $enum
     * @return string
     */
    public static function getLabelFrom(BackedEnum|string $enum): string
    {
        if(is_string($enum)){
            $enum = self::from($enum);
        }

        return match ($enum) {
            self::Parcel => 'Parcela',//translate
            self::City => 'Ciudad',//translate
            default => '-Seleccione-'//translate
        };
    }

}
