<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;
use BackedEnum;

enum SubsystemFunctionalClassification: string
{
    use EnumsTrait;

    case Null = '';
    case Residential = '0';
    case Local = '1';
    case City = '2';
    case Enterprise = '3';

    const CHOICES = [self::Residential, self::Local, self::City, self::Enterprise];

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
            self::Residential => 'Residencial',//translate
            self::Local => 'Servicios Básicos Locales',//translate
            self::City => 'Serrvicios de Escala de Ciudad',//translate
            self::Enterprise => 'Instalación productiva-empresarial',//translate
            default => '-Seleccione-'//translate
        };
    }

}
