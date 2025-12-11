<?php

namespace App\Component\Twig\Card;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'component/twig/card/card.html.twig')]
final class Card
{
    public const BACKDROP_DATA_ID = 'card-backdrop';

    public string $cssClass = '';
    public string $extra = '';
    public string $headerCssClass = '';
    public string $title = '';
    public string $icon = '';
}
