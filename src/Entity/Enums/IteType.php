<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;

enum IteType: string
{
    use EnumsTrait;

    case Null = '';
    case National = '0';
    case International = '1';

    public const CHOICES = [self::National, self::International];

    public static function getLabelFrom(\BackedEnum|string $enum): string
    {
        if (is_string($enum)) {
            $enum = self::from($enum);
        }

        return match ($enum) {
            self::National => 'Nacional',// translate
            self::International => 'Internacional',// translate
            default => '-Seleccione-',// translate
        };
    }
}
