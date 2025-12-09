<?php

namespace App\Entity\Interfaces;

interface MoneyInterface
{
    public function getPrice(): int|float;

    public function getCurrency(): ?string;
}
