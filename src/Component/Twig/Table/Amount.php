<?php

namespace App\Component\Twig\Table;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'component/twig/table/amount.html.twig')]
final class Amount
{
    public string $label = 'Mostrar';
    /** @var array<mixed>  */
    public array $options = [];
    public int $amount = 0;
    public string $queryName = 'amount';
}
