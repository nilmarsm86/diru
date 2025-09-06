<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;
use BackedEnum;

enum NetworkConnectionType: string
{
    use EnumsTrait;

    case Null = '';
    case Outside = '0';
    case Inside = '1';

    const CHOICES = [self::Outside, self::Inside];

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
            self::Outside => 'Externa',//translate
            self::Inside => 'Interna',//translate
            default => '-Seleccione-'//translate
        };
    }

}
