<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;

enum BuildingState: string
{
    use EnumsTrait;

    case Null = '';
    case Registered = '1'; // cuando se pusieron los datos de la obra
    //    case Initiated = '2'; // cuando se le pone los datos del terreno
    //    case TerrainDiagnosis = '3';
    //    case UrbanRegulation = '4';
    case Design = '3';
    //    case Stopped = '0';
    //    case Canceled = '1';
    case Diagnosis = '2';
    case Revision = '4';
    case Revised = '5';
    //    case PresupuestoEstiamdo = '10';
    //    case PresupuestoDetallado = '11';
    //    case Ejecucion = '12';

    public const CHOICES = [self::Registered, self::Diagnosis, self::Design, self::Revision, self::Revised];

    public static function getLabelFrom(\BackedEnum|string $enum): string
    {
        if (is_string($enum)) {
            $enum = self::from($enum);
        }

        return match ($enum) {
            self::Registered => 'Registrado',// translate
            self::Diagnosis => 'Evaluacion / Diagnóstico',// translate
            self::Design => 'Diseño',// translate
            self::Revision => 'Revisión',// translate
            self::Revised => 'Revisado',// translate
            //            self::Initiated => 'Iniciado',// translate

            //            self::UrbanRegulation => 'Regulación urbana',// translate

            default => '-Seleccione-',// translate
        };
    }
}
