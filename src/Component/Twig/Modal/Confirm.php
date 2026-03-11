<?php

namespace App\Component\Twig\Modal;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'component/twig/modal/confirm.html.twig')]
final class Confirm
{
    public const MODAL_CLOSE = 'modal_close';
    public const BACKDROP_DATA_ID = 'modal-backdrop';

    public string $title;
    public string $question;
    public string $id;
    public string $href;
}
