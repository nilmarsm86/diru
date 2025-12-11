<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;

enum StructureState: string
{
    use EnumsTrait;

    case Recent = '0'; // Estructura en un inmueble nuevo
    case ExistingWithoutReplicating = '1'; // Estructura en un inmueble existente a la que NO se le a realizado replica
    case ExistingReplicated = '2'; // Estructura en un inmueble existente, a la que se le ha realizado replica
    case Replica = '3'; // Replica de un inmueble existente

    public const CHOICES = [self::Recent, self::ExistingWithoutReplicating, self::ExistingReplicated, self::Replica];

    public static function getLabelFrom(\BackedEnum|string $enum): string
    {
        if (is_string($enum)) {
            $enum = self::from($enum);
        }

        return match ($enum) {
            self::Recent => 'Nuevo',// translate
            self::ExistingWithoutReplicating => 'Existente sin replicar',// translate
            self::ExistingReplicated => 'Existente replicado',// translate
            self::Replica => 'Réplica',// translate
            default => '-Seleccione-',// translate
        };
    }
}
