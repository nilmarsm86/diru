<?php

namespace App\Component\Twig\Card;

use App\DTO\EnumSimulator;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'component/twig/card/filter_drop_down.html.twig')]
final class FilterDropDown
{
    public string $path = '';
    /** @var array<mixed> */
    public array $pathParams = [];
    public string $label = '';
    /** @var array<object> */
    public array $options = [];
    public string $data = '';
    public string $queryName = 'type';

    /**
     * @param array<object> $options
     */
    public function mount(array $options = []): void
    {
        $this->options = $options;
        $enumSimulators = [];
        $wrapper = false;
        foreach ($options as $option) {
            if (false === property_exists($option, 'value')) {
                $wrapper = true;
                $enumSimulators[] = new EnumSimulator($option);
            }
        }

        if ($wrapper) {
            $this->options = $enumSimulators;
        }
    }
}
