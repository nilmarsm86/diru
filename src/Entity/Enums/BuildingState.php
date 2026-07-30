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
    case Diagnosis = '2';
    case Design = '3';
    //    case Stopped = '0';
    //    case Canceled = '1';

    case RevisionDraftman = '4';
    case RevisionInvestmen = '6';
    case RevisionDirector = '7';
    case RevisedDraftman = '5';
    case RevisedInvestmen = '8';
    case RevisedDirector = '9';
    //    case PresupuestoEstiamdo = '10';
    //    case PresupuestoDetallado = '11';
    //    case Ejecucion = '12';

    public const CHOICES = [self::Registered, self::Diagnosis, self::Design, self::RevisionDraftman, self::RevisedDraftman, self::RevisionInvestmen, self::RevisedInvestmen, self::RevisionDirector, self::RevisedDirector];

    public static function getLabelFrom(\BackedEnum|string $enum): string
    {
        if (is_string($enum)) {
            $enum = self::from($enum);
        }

        return match ($enum) {
            self::Registered => 'Registrado',// translate
            self::Diagnosis => 'Evaluacion / Diagnóstico',// translate
            self::Design => 'Diseño',// translate
            self::RevisionDraftman => 'Revisión Proyectista',// translate
            self::RevisedDraftman => 'Revisado por Proyectista',// translate
            self::RevisionInvestmen => 'Revisión Inversor',// translate
            self::RevisedInvestmen => 'Revisado por Inversor',// translate
            self::RevisionDirector => 'Revisión Director',// translate
            self::RevisedDirector => 'Revisado Director',// translate
            //            self::Initiated => 'Iniciado',// translate

            //            self::UrbanRegulation => 'Regulación urbana',// translate

            default => '-Seleccione-',// translate
        };
    }
}
