<?php

namespace App\Component\Twig;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'component/twig/delete_form.html.twig')]
final class DeleteForm
{
    public string $path;
    public string $confirm = 'Está seguro que desea borrarlo?';
    public string $token;
    public string $title;
    public string $icon;
    public string $label = '';
    public bool $active = true;
    public string $cssClass = 'btn-danger';
}
