<?php

namespace App\Component\Twig\Backdrop;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'component/twig/backdrop/backdrop.html.twig')]
final class Backdrop
{
    public string $id;
    public string $attr = '';
}
