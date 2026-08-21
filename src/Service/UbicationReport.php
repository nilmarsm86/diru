<?php

namespace App\Service;

use App\DTO\Paginator;
use App\Repository\Interfaces\UbicationInterface;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class UbicationReport
{
    /**
     * @return RedirectResponse|array<mixed>
     */
    public function amountProjectsAndBuildings(
        Request $request,
        RouterInterface $router,
        UbicationInterface $ubicationRepository,
        bool $pdf = false,
    ): RedirectResponse|array {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

        if (true === $pdf) {
            $amountPerPage = null;
            $pageNumber = null;
        }

        $data = $ubicationRepository->findAmountProject($filter, $amountPerPage, $pageNumber);
        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        return [$filter, $paginator];
    }

    /**
     * @return RedirectResponse|array<mixed>
     *
     * @throws Exception
     */
    public function amountClients(Request $request, RouterInterface $router, UbicationInterface $ubicationRepository, bool $pdf = false): RedirectResponse|array
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

        if (true === $pdf) {
            $amountPerPage = null;
            $pageNumber = null;
        }

        $data = $ubicationRepository->findAmountClients($filter, $amountPerPage, $pageNumber);

        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        return [$filter, $paginator];
    }
}
