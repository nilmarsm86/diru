<?php

namespace App\Component\Twig\Backdrop;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'component/twig/backdrop/backdrop.html.twig')]
final class Backdrop
{
    public string $id;
    public string $attr = '';

//    #[LiveListener(self::SHOW)]
//    public function onShow(#[LiveArg] string $id): void
//    {
//        $this->dispatchBrowserEvent(self::SHOW, [
//            'id' => $id
//        ]);
//    }
//
//    #[LiveListener(self::HIDE)]
//    public function onHide(#[LiveArg] string $id): void
//    {
//        $this->dispatchBrowserEvent(self::HIDE, [
//            'id' => $id
//        ]);
//    }

}
