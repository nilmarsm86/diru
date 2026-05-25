<?php

namespace App\Controller;

use App\DTO\Paginator;
use App\Entity\City;
use App\Entity\Country;
use App\Entity\Enums\CorporateEntityType;
use App\Entity\Enums\IteQuality;
use App\Entity\Enums\IteType;
use App\Entity\Ite;
use App\Entity\IteProjectType;
use App\Entity\IteSource;
use App\Entity\MeasurementUnit;
use App\Repository\IteRepository;
use App\Repository\IteSourceRepository;
use App\Repository\MeasurementUnitRepository;
use App\Service\CrudActionService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Route('/ite_manage')]
final class IteController extends AbstractController
{
    //    #[Route(name: 'app_ite_index', methods: ['GET'])]
    //    public function index(Request $request, RouterInterface $router, IteRepository $iteRepository): Response
    //    {
    //        $filter = $request->query->get('filter', '');
    //        $amountPerPage = (int) $request->query->get('amount', '10');
    //        $pageNumber = (int) $request->query->get('page', '1');
    //
    //        //        $type = $request->query->get('entity', '');
    //
    //        $data = $iteRepository->findItes($filter, $amountPerPage, $pageNumber);
    //
    //        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
    //        if ($paginator->isFromGreaterThanTotal()) {
    //            return $paginator->greatherThanTotal($request, $router, $pageNumber);
    //        }
    //
    //        $template = ($request->isXmlHttpRequest()) ? '_list.html.twig' : 'index.html.twig';
    //
    //        return $this->render("ite/$template", [
    //            'filter' => $filter,
    //            'paginator' => $paginator,
    //            //            'types' => CorporateEntityType::cases(),
    //        ]);
    //    }

    #[Route('/national', name: 'app_ite_national', methods: ['GET'])]
    public function national(Request $request, RouterInterface $router, EntityManagerInterface $entityManager): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

        $quality = $request->query->get('quality', '');
        $measurementUnit = $request->query->get('mu', '');
        $source = $request->query->get('source', '');
        $projectType = $request->query->get('pt', '');
        $city = $request->query->get('city', '');
        $country = $request->query->get('country', '');

        $data = $entityManager->getRepository(Ite::class)->findItes($filter, $amountPerPage, $pageNumber, IteType::National, $quality, $measurementUnit, $source, $projectType, $city, $country);

        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        $template = ($request->isXmlHttpRequest()) ? '_list.html.twig' : 'index.html.twig';

        return $this->render("ite/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'type' => IteType::National,
            'path' => 'app_ite_national',
            'qualities' => IteQuality::cases(),
            'measurementUnits' => $entityManager->getRepository(MeasurementUnit::class)->findAll(),
            'sources' => $entityManager->getRepository(IteSource::class)->findAll(),
            'projectTypes' => $entityManager->getRepository(IteProjectType::class)->findAll(),
            'cities' => $entityManager->getRepository(City::class)->findBy([], ['name' => 'ASC']),
            'countries' => $entityManager->getRepository(Country::class)->findBy([], ['name' => 'ASC']),
        ]);
    }

    #[Route('/international', name: 'app_ite_international', methods: ['GET'])]
    public function international(Request $request, RouterInterface $router, EntityManagerInterface $entityManager): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = (int) $request->query->get('amount', '10');
        $pageNumber = (int) $request->query->get('page', '1');

        $quality = $request->query->get('quality', '');
        $measurementUnit = $request->query->get('mu', '');
        $source = $request->query->get('source', '');
        $projectType = $request->query->get('pt', '');
        $city = $request->query->get('city', '');
        $country = $request->query->get('country', '');

        $data = $entityManager->getRepository(Ite::class)->findItes($filter, $amountPerPage, $pageNumber, IteType::International, $quality, $measurementUnit, $source, $projectType, $city, $country);

        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->isFromGreaterThanTotal()) {
            return $paginator->greatherThanTotal($request, $router, $pageNumber);
        }

        $template = ($request->isXmlHttpRequest()) ? '_list.html.twig' : 'index.html.twig';

        return $this->render("ite/$template", [
            'filter' => $filter,
            'paginator' => $paginator,
            'type' => IteType::International,
            'path' => 'app_ite_international',
            'qualities' => IteQuality::cases(),
            'measurementUnits' => $entityManager->getRepository(MeasurementUnit::class)->findAll(),
            'sources' => $entityManager->getRepository(IteSource::class)->findAll(),
            'projectTypes' => $entityManager->getRepository(IteProjectType::class)->findAll(),
            'cities' => $entityManager->getRepository(City::class)->findBy([], ['name' => 'ASC']),
            'countries' => $entityManager->getRepository(Country::class)->findBy([], ['name' => 'ASC']),
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/new', name: 'app_ite_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudActionService $crudActionService): Response
    {
        $ite = new Ite();

        return $crudActionService->formLiveComponentAction($request, $ite, 'ite', [
            'title' => 'Nuevo Indicador Técnico Económico',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_ite_show', methods: ['GET'])]
    public function show(Request $request, Ite $ite, CrudActionService $crudActionService): Response
    {
        return $crudActionService->showAction($request, $ite, 'ite', 'ite', 'Detalles del ITE');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}/edit', name: 'app_ite_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ite $ite, CrudActionService $crudActionService): Response
    {
        return $crudActionService->formLiveComponentAction($request, $ite, 'ite', [
            'title' => 'Editar Indicador Técnico Económico',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/{id}', name: 'app_ite_delete', methods: ['POST'])]
    public function delete(Request $request, Ite $ite, IteRepository $iteRepository, CrudActionService $crudActionService): Response
    {
        $successMsg = 'Se ha eliminado el inidicador.';
        $response = $crudActionService->deleteAction($request, $iteRepository, $ite, $successMsg, 'app_ite_index');
        if ($response instanceof RedirectResponse) {
            $this->addFlash('success', $successMsg);

            return $response;
        }

        return $response;
    }
}
