<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\StimulusControllerTwigExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StimulusControllerTwigExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('stimulusController', [StimulusControllerTwigExtensionRuntime::class, 'renderStimulusController'], ['is_safe' => ['html_attr']]),
            new TwigFunction('stimulusAction', [StimulusControllerTwigExtensionRuntime::class, 'renderStimulusAction'], ['is_safe' => ['html_attr']]),
            new TwigFunction('stimulusTarget', [StimulusControllerTwigExtensionRuntime::class, 'renderStimulusTarget'], ['is_safe' => ['html_attr']]),
        ];
    }
}
