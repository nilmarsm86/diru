<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\PdfTwigExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PdfTwigExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            //            new TwigFilter('filter_name', [TableListTwigExtensionRuntime::class, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('th', [PdfTwigExtensionRuntime::class, 'th'], ['is_safe' => ['html']]),
            new TwigFunction('td', [PdfTwigExtensionRuntime::class, 'td'], ['is_safe' => ['html']]),
        ];
    }
}
