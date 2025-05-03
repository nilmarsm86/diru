<?php

namespace App\Component\Twig;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'component/twig/manage_page.html.twig')]
final class ManagePage extends AbstractController
{

}
