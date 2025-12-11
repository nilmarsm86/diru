<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;

enum LocalType: string
{
    use EnumsTrait;

    case Null = '';
    case EmptyArea = '0';
    case Local = '1';
    case WallArea = '2';

    public const CHOICES = [self::EmptyArea, self::Local, self::WallArea];

    public static function getLabelFrom(\BackedEnum|string $enum): string
    {
        if (is_string($enum)) {
            $enum = self::from($enum);
        }

        return match ($enum) {
            self::Local => 'Área útil (local)',// translate
            self::WallArea => 'Área de muro',// translate
            self::EmptyArea => 'Área de vacío',// translate
            default => '-Seleccione-',// translate
        };
    }
}
