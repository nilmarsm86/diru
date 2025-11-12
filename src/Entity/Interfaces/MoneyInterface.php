<?php

namespace App\Entity\Interfaces;

interface MoneyInterface
{
    public function getPrice(): ?int;

    public function getCurrency(): ?string;
}
