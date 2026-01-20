<?php

namespace App\Component\Twig\Table;

use App\DTO\Paginator;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'component/twig/table/table.html.twig')]
final class Table
{
    public const BACKDROP_DATA_ID = 'table-backdrop';

    public ?Paginator $paginator = null;
    public string $tableContainer = '';
    public bool $amount = true;
    public bool $filter = true;
    public bool $showPage = true;
    public bool $navigation = true;

    /**
     * cuando se monta por primera vez el componete.
     */
    public function mount(?Paginator $paginator = null): void
    {
        $this->paginator = $paginator;
    }
}
