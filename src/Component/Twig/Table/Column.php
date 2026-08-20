<?php

namespace App\Component\Twig\Table;

use App\DTO\EnumSimulator;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'component/twig/table/column.html.twig')]
final class Column
{
    public string $path = '';
    /** @var array<mixed> */
    public array $pathParams = [];
    public string $label = 'Columnas';
    /** @var array<object> */
    public array $columns = [];
    public string $data = '';
    public string $queryName = 'column';

    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    /**
     * @param array<object> $columns
     */
    public function mount(array $columns = []): void
    {
        $this->columns = $columns;
        $enumSimulators = [];
        $wrapper = false;
        foreach ($columns as $option) {
            if (is_null($option)) {
                $wrapper = true;
                $enumSimulator = new EnumSimulator($option);
                $enumSimulators[] = $enumSimulator;
            } else {
                if (false === property_exists($option, 'value')) {
                    $wrapper = true;
                    $enumSimulators[] = new EnumSimulator($option);
                }
            }
        }

        if ($wrapper) {
            $this->columns = $enumSimulators;
        }
    }

    public function columns(): mixed
    {
        $data = $this->requestStack->getMainRequest()?->query->get($this->queryName, '') ?? '';

        return json_decode(urldecode($data), true);
    }
}
