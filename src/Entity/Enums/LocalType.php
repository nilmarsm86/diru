<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;
use BackedEnum;

enum LocalType: string
{
    use EnumsTrait;

    case Null = '';
    case EmptyArea = '0';
    case Local = '1';
    case WallArea = '2';

    const CHOICES = [self::EmptyArea, self::Local, self::WallArea];

    /**
     * @param BackedEnum|string $enum
     * @return string
     */
    public static function getLabelFrom(BackedEnum|string $enum): string
    {
        if(is_string($enum)){
            $enum = self::from($enum);
        }

        return match ($enum) {
            self::EmptyArea => 'Área de vacío',//translate
            self::Local => 'Local',//translate
            self::WallArea => 'Área de muro',//translate
            default => '-Seleccione-'//translate
        };
    }

}
