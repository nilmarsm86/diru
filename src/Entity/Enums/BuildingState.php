<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;

enum BuildingState: string
{
    use EnumsTrait;

    case Null = '';
    case Registered = '6';//cuando se pusieron los datos de la obra
    case Initiated = '2';//cuando se le pone los datos del terreno
//    case TerrainDiagnosis = '3';
//    case UrbanRegulation = '4';
    case Design = '5';
    case Stopped = '0';
    case Canceled = '1';
    case Diagnosis = '7';
//    case Revision = '8';
//    case Aprobado = '9';
//    case PresupuestoEstiamdo = '10';
//    case PresupuestoDetallado = '11';
//    case Ejecucion = '12';

    public const CHOICES = [self::Stopped, self::Canceled, self::Initiated, self::Diagnosis, /*self::UrbanRegulation,*/ self::Design, self::Registered];

    public static function getLabelFrom(\BackedEnum|string $enum): string
    {
        if (is_string($enum)) {
            $enum = self::from($enum);
        }

        return match ($enum) {
            self::Stopped => 'Detenido',// translate
            self::Canceled => 'Cancelado',// translate
            self::Initiated => 'Iniciado',// translate
            self::Diagnosis => 'Diagnóstico',// translate
//            self::UrbanRegulation => 'Regulación urbana',// translate
            self::Design => 'Diseño',// translate
            self::Registered => 'Registrado',// translate
            default => '-Seleccione-',// translate
        };
    }
}
