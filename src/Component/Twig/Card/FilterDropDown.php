<?php

namespace App\Component\Twig\Card;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'component/twig/card/filter_drop_down.html.twig')]
final class FilterDropDown
{
    public string $path = '';
    /** @var array<mixed>  */
    public array $pathParams = [];
    public string $label = '';
    /** @var array<string>  */
    public array $options = [];
    public string $data = '';
    public string $queryName = 'type';
}
