<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;

enum CorporateEntityType: string
{
    use EnumsTrait;

    case Null = '';
    case Client = '0';
    case Constructor = '1';
    case ClientAndConstructor = '2';

    public const CHOICES = [self::Client, self::Constructor, self::ClientAndConstructor];

    public static function getLabelFrom(\BackedEnum|string $enum): string
    {
        if (is_string($enum)) {
            $enum = self::from($enum);
        }

        return match ($enum) {
            self::Client => 'Cliente',// translate
            self::Constructor => 'Constructora',// translate
            self::ClientAndConstructor => 'Cliente y Constructora',// translate
            default => '-Seleccione-',// translate
        };
    }
}
