<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;

enum NetworkConnectionType: string
{
    use EnumsTrait;

    case Null = '';
    case Outside = '0';
    case Inside = '1';

    public const CHOICES = [self::Outside, self::Inside];

    public static function getLabelFrom(\BackedEnum|string $enum): string
    {
        if (is_string($enum)) {
            $enum = self::from($enum);
        }

        return match ($enum) {
            self::Outside => 'Externa',// translate
            self::Inside => 'Interna',// translate
            default => '-Seleccione-',// translate
        };
    }
}
