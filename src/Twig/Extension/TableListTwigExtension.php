<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\TableListTwigExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TableListTwigExtension extends AbstractExtension
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
            new TwigFunction('show_reload', [TableListTwigExtensionRuntime::class, 'showReload']),
        ];
    }
}
