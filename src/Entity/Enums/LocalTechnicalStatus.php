<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;
use BackedEnum;

enum LocalTechnicalStatus: string
{
    use EnumsTrait;

    case Null = '';
    case Undefined = '0';
    case Critical = '1';
    case Bad = '2';
    case Regular = '3';
    case Good = '4';

    const CHOICES = [self::Undefined, self::Critical, self::Bad, self::Regular, self::Good];

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
            self::Undefined => 'Sin definir',//translate
            self::Critical => 'Crítico',//translate
            self::Bad => 'Malo',//translate
            self::Regular => 'Regular',//translate
            self::Good => 'Bueno',//translate
            default => '-Seleccione-'//translate
        };
    }

}
