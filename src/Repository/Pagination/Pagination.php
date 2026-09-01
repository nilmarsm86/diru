<?php

namespace App\Repository\Pagination;

final readonly class Pagination
{
    public function __construct(
        public int $page = 1,
        public int $amountPerPage = 10,
    ) {
        if ($this->page < 1) {
            throw new \InvalidArgumentException('El número de pagina debe ser >= 1');
        }
        if ($this->amountPerPage < 1) {
            throw new \InvalidArgumentException('La cantidad por página debe ser >= 1');
        }
    }
}
