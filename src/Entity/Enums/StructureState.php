<?php

namespace App\Entity\Enums;

use App\Entity\Traits\EnumsTrait;
use BackedEnum;

enum StructureState: string
{
    use EnumsTrait;

    case Recent  = '0';
    case ExistingWithoutReplicating = '1';
    case ExistingReplicated = '2';
    case Replica = '3';

    const CHOICES = [self::Recent, self::ExistingWithoutReplicating, self::ExistingReplicated, self::Replica];

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
            self::Recent => 'Nuevo',//translate
            self::ExistingWithoutReplicating => 'Existente sin replicar',//translate
            self::ExistingReplicated => 'Existente replicado',//translate
            self::Replica => 'Réplica',//translate
            default => '-Seleccione-'//translate
        };
    }
}
