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
    case Draftman = '3';

    public const CHOICES = [self::Client, self::Constructor, self::ClientAndConstructor, self::Draftman];

    public static function getLabelFrom(\BackedEnum|string $enum): string
    {
        if (is_string($enum)) {
            $enum = self::from($enum);
        }

        return match ($enum) {
            self::Client => 'Cliente',// translate
            self::Constructor => 'Constructora',// translate
            self::ClientAndConstructor => 'Cliente y Constructora',// translate
            self::Draftman => 'Proyectista',// translate
            default => '-Seleccione-',// translate
        };
    }
}
