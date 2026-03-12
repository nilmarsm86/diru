<?php

namespace App\DTO;

class EnumSimulator
{
    public string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getLabelFrom(EnumSimulator $enum): string
    {
        return $this->value;
    }
}
