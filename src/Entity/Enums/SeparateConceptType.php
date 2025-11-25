<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;
use BackedEnum;

enum SeparateConceptType: string
{
    use EnumsTrait;

    case Null = '';
    case Branch = '0';
    case Leaf = '1';
    case Computable = '2';

    const CHOICES = [self::Branch, self::Leaf, self::Computable];

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
            self::Branch => 'Rama',//translate
            self::Leaf => 'Hoja',//translate
            self::Computable => 'Computable',//translate
            default => '-Seleccione-'//translate
        };
    }

}
