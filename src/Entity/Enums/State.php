<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;

enum State: string
{
    use EnumsTrait;

    case Null = '';
    case Active = '1';
    case Inactive = '0';

    public static function getLabelFrom(\BackedEnum|string $enum): string
    {
        if (is_string($enum)) {
            $enum = self::from($enum);
        }

        return match ($enum) {
            self::Active => 'Activo',// translate
            self::Inactive => 'Inactivo',// translate
            default => '-Seleccione-',// translate
        };
    }
}
