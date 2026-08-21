<?php

namespace App\Repository\Interfaces;

use Doctrine\ORM\Tools\Pagination\Paginator;

interface UbicationInterface
{
    /**
     * @return Paginator<mixed>
     */
    public function findAmountProject(string $filter = '', ?int $amountPerPage = 10, ?int $page = 1): Paginator;

    /**
     * @return Paginator<mixed>
     */
    public function findAmountClients(string $filter = '', ?int $amountPerPage = 10, ?int $page = 1): Paginator;
}
