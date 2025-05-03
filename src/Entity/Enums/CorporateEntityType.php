<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;
use BackedEnum;

enum CorporateEntityType: string
{
    use EnumsTrait;

    case Null = '';
    case Client = '0';
    case Constructor = '1';
    case ClientAndConstructor = '2';

    const CHOICES = [self::Client, self::Constructor, self::ClientAndConstructor];

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
            self::Client => 'Cliente',//translate
            self::Constructor => 'Constructora',//translate
            self::ClientAndConstructor => 'Cliente y Constructora',//translate
            default => '-Seleccione-'//translate
        };
    }

//    /**
//     * @return CorporateEntityType[]
//     */
//    public static function getChoices(): array
//    {
//        return [self::Client, self::Constructor, self::ClientAndConstructor];
//    }
}
