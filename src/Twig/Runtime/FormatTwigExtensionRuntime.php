<?php

namespace App\Twig\Runtime;

use App\Entity\Interfaces\MoneyInterface;
use Twig\Extension\RuntimeExtensionInterface;

class FormatTwigExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function money(mixed $price, $currency = null): string
    {
        if (gettype($price) === 'object' && $price instanceof MoneyInterface) {
            return number_format(((float)$price->getPrice() / 100), 2) . ' ' . $price->getCurrency();
        } else {
            return number_format(((float)$price / 100), 2) . ' ' . (!is_null($currency) ? $currency : '');
        }
    }
}
