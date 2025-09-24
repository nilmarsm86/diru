<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;
use BackedEnum;

enum UrbanRegulationStructure: string
{
    use EnumsTrait;

    case Local = '0';
    case SubSystem = '1';
    case Floor = '2';
    case Building = '3';
    case Project = '4';

    const CHOICES = [self::Local, self::SubSystem, self::Floor, self::Building, self::Project];

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
            self::Local => 'Local',//translate
            self::SubSystem => 'Subsistema',//translate
            self::Floor => 'Planta',//translate
            self::Building => 'Obra',//translate
            self::Project => 'Proyecto',//translate
            default => '-Seleccione-'//translate
        };
    }
}
