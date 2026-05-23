<?php

namespace App\DTO;

class EnumSimulator
{
    public mixed $value;

    public function __construct(mixed $value)
    {
        $this->value = $value;
    }

    public function getLabelFrom(?EnumSimulator $enum = null): mixed
    {
        if (null !== $enum) {
            return $enum->value;
        }

        return $this->value;
    }
}
