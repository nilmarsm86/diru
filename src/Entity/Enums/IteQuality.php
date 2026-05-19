<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;

enum IteQuality: string
{
    use EnumsTrait;

    case Null = '';
    case Medium = '0';
    case Standard = '1';
    case MediumHight = '2';
    case Hight = '3';

    public const CHOICES = [self::Medium, self::Standard, self::MediumHight, self::Hight];

    public static function getLabelFrom(\BackedEnum|string $enum): string
    {
        if (is_string($enum)) {
            $enum = self::from($enum);
        }

        return match ($enum) {
            self::Medium => 'Media',// translate
            self::Standard => 'Estándar',// translate
            self::MediumHight => 'Media - Alta',// translate
            self::Hight => 'Alta',// translate
            default => '-Seleccione-',// translate
        };
    }

    public static function getFromLabel(string $label): IteQuality
    {
        return match ($label) {
            'Estándar' => self::Standard,
            'Media-Alta' => self::MediumHight,
            'Alta' => self::Hight,
            default => self::Medium,
        };
    }
}
