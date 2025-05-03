<?php

namespace App\Component\Twig\Table;

use App\DTO\Paginator;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'component/twig/table/table.html.twig')]
final class Table
{
    const BACKDROP_DATA_ID = 'table-backdrop';

    public ?Paginator $paginator = null;
    public string $tableContainer = '';

    /**
     * cuando se monta por primera vez el componete
     * @param Paginator $paginator
     * @return void
     */
    public function mount(Paginator $paginator): void
    {
        $this->paginator = $paginator;
    }

}
