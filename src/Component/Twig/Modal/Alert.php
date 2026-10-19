<?php

namespace App\Component\Twig\Modal;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'component/twig/modal/alert.html.twig')]
final class Alert
{
    public const MODAL_CLOSE = 'modal_close';
    public const BACKDROP_DATA_ID = 'modal-backdrop';

    public string $title;
    public string $text;
    public string $id;
    public string $href;
}
