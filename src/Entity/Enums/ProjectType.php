<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;

enum ProjectType: string
{
    use EnumsTrait;

    case Null = '';
    case Parcel = '0';
    case City = '1';

    public const CHOICES = [self::Parcel, self::City];

    public static function getLabelFrom(\BackedEnum|string $enum): string
    {
        if (is_string($enum)) {
            $enum = self::from($enum);
        }

        return match ($enum) {
            self::Parcel => 'Parcela',// translate
            self::City => 'Ciudad',// translate
            default => '-Seleccione-',// translate
        };
    }
}
