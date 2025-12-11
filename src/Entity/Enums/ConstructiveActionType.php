<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;

enum ConstructiveActionType: string
{
    use EnumsTrait;

    case Null = '';
    case NoModifier = '0';
    case Modifier = '1';

    public const CHOICES = [self::NoModifier, self::Modifier];

    public static function getLabelFrom(\BackedEnum|string $enum): string
    {
        if (is_string($enum)) {
            $enum = self::from($enum);
        }

        return match ($enum) {
            self::NoModifier => 'No modificadora',// translate
            self::Modifier => 'Modificadora',// translate
            default => '-Seleccione-',// translate
        };
    }
}
