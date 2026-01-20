<?php

namespace App\Twig\Runtime;

use App\Entity\Interfaces\MoneyInterface;
use Twig\Extension\RuntimeExtensionInterface;

class FormatTwigExtensionRuntime implements RuntimeExtensionInterface
{
    public function money(MoneyInterface|string $price, ?string $currency = null): string
    {
        if (!is_string($price)) {
            return number_format((float) $price->getPrice() / 100, 2).' '.$price->getCurrency();
        } else {
            return number_format((float) $price / 100, 2).' '.(!is_null($currency) ? $currency : '');
        }
    }
}
