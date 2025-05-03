<?php

namespace App\Component\Twig\Table;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'component/twig/table/filter.html.twig')]
final class Filter
{
    public string $filter = '';
    public string $queryName = 'filter';
    public string $placeholder = 'Buscar...';
}
