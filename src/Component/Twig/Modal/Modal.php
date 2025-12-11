<?php

namespace App\Component\Twig\Modal;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'component/twig/modal/modal.html.twig')]
final class Modal
{
    public const MODAL_CLOSE = 'modal_close';
    public const BACKDROP_DATA_ID = 'modal-backdrop';

    public string $title;
    public string $id;
}
