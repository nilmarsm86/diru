<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;
use BackedEnum;

enum BuildingState: string
{
    use EnumsTrait;

    case Null = '';
    case Registered = '6';
    case Initiated = '2';
    case TerrainDiagnosis = '3';
    case UrbanRegulation = '4';
    case Design = '5';
    case Stopped = '0';
    case Canceled = '1';

    const CHOICES = [self::Stopped, self::Canceled, self::Initiated, self::TerrainDiagnosis, self::UrbanRegulation, self::Design, self::Registered];

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
            self::Stopped => 'Detenido',//translate
            self::Canceled => 'Cancelado',//translate
            self::Initiated => 'Iniciado',//translate
            self::TerrainDiagnosis => 'Diagnóstico de terreno',//translate
            self::UrbanRegulation => 'Regulación urbana',//translate
            self::Design => 'Diseño',//translate
            self::Registered => 'Registrado',//translate
            default => '-Seleccione-'//translate
        };
    }
}
