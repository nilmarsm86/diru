<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;

enum TechnicalStatus: string
{
    use EnumsTrait;

    case Null = '';
    case Undefined = '0';
    case Critical = '1';
    case Bad = '2';
    case Regular = '3';
    case Good = '4';

    public const CHOICES = [self::Undefined, self::Critical, self::Bad, self::Regular, self::Good];

    public static function getLabelFrom(\BackedEnum|string $enum): string
    {
        if (is_string($enum)) {
            $enum = self::from($enum);
        }

        return match ($enum) {
            self::Undefined => 'Sin definir',// translate
            self::Critical => 'Crítico',// translate
            self::Bad => 'Malo',// translate
            self::Regular => 'Regular',// translate
            self::Good => 'Bueno',// translate
            default => '-Seleccione-',// translate
        };
    }

    public static function getBackground(\BackedEnum|string $enum): string
    {
        if (is_string($enum)) {
            $enum = self::from($enum);
        }

        return match ($enum) {
            //            self::Undefined => '',// translate
            self::Critical => 'bg-danger',// translate
            self::Bad => 'bg-warning',// translate
            self::Regular => 'bg-info',// translate
            self::Good => 'bg-success',// translate
            default => '',// translate
        };
    }
}
