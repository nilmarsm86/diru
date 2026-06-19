<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Building;
use App\Entity\BuildingSeparateConcept;
use App\Entity\Project;
use App\Form\BuildingType;
use App\Repository\BuildingRepository;
use App\Repository\ClientRepository;
use App\Repository\CorporateEntityRepository;
use App\Repository\DraftsmanRepository;
use App\Repository\EnterpriseClientRepository;
use App\Repository\IndividualClientRepository;
use App\Repository\ProjectRepository;
use App\Repository\SeparateConceptRepository;
use App\Service\Building\BuildingValuationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent(template: 'component/live/building_form.html.twig')]
final class BuildingForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;
    use LiveCollectionTrait;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?Building $bui = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp(writable: true)]
    public ?int $constructor = 0;

    #[LiveProp(writable: true)]
    public ?int $corporateEntity = 0;

    #[LiveProp(writable: true)]
    public ?int $project = 0;

    #[LiveProp(writable: true)]
    public ?float $urbanizationEstimateTotalPrice = 0;

    #[LiveProp(writable: true)]
    public ?float $ptpEstimateTotalPrice = 0;

    #[LiveProp(writable: true)]
    public ?float $justValueEstimateTotalPrice = 0;

    #[LiveProp(writable: true)]
    public ?int $client = 0;

    #[LiveProp(writable: true)]
    public ?int $individualClient = 0;

    #[LiveProp(writable: true)]
    public ?int $enterpriseClient = 0;

    public function __construct(
        private readonly ProjectRepository $projectRepository,
        private readonly IndividualClientRepository $individualClientRepository,
        private readonly EnterpriseClientRepository $enterpriseClientRepository,
        private readonly BuildingValuationService $buildingValuationService,
    ) {
    }

    public function mount(?Building $bui = null): void
    {
        $this->bui = (is_null($bui)) ? new Building() : $bui;
        if (!is_null($this->bui->getId())) {
            $this->urbanizationEstimateTotalPrice = $this->bui->getUrbanizationEstimateTotalPrice();
            $this->ptpEstimateTotalPrice = $this->bui->getProjectTechnicalPreparationEstimateTotalPrice();
        }
    }

    //    public function preValue(): void
    //    {
    //        if (0 !== $this->constructor) {
    //            $this->formValues['constructor'] = (string) $this->constructor;
    //            $this->constructor = 0;
    //        }
    //
    //        if (0 !== $this->corporateEntity) {
    //            $this->formValues['corporateEntity'] = (string) $this->corporateEntity;
    //            $this->corporateEntity = 0;
    //        }
    //
    //        if (0 !== $this->project) {
    //            $project = $this->projectRepository->find((int) $this->project);
    //            $this->bui?->setProject($project);
    //        }
    //
    //        if (0.0 !== $this->urbanizationEstimateTotalPrice) {
    //            $this->formValues['estimatedValueUrbanization'] = (float) $this->urbanizationEstimateTotalPrice / 100;
    //        }
    //
    //        if (0.0 !== $this->ptpEstimateTotalPrice) {
    //            $this->formValues['projectPriceTechnicalPreparation'] = (float) $this->ptpEstimateTotalPrice / 100;
    //        }
    //
    //        if (0.0 !== $this->justValueEstimateTotalPrice) {
    //            $this->formValues['estimatedJustValue'] = (float) $this->justValueEstimateTotalPrice / 100;
    //        }
    //
    //        if (!is_null($this->bui?->getId())) {
    //            if (isset($this->formValues['individualClient']) && '' !== $this->formValues['individualClient']) {
    //                /** @var int $individualClient */
    //                $individualClient = $this->formValues['individualClient'];
    //                $this->individualClient = $individualClient;
    //            }
    //        } else {
    //            if (isset($this->formValues['individualClient']) && '' !== $this->formValues['individualClient']) {
    //                /** @var int $individualClient */
    //                $individualClient = $this->formValues['individualClient'];
    //                if ((int) $this->individualClient > $individualClient) {
    //                    $this->formValues['individualClient'] = (string) $this->individualClient;
    //                } else {
    //                    $this->individualClient = $individualClient;
    //                }
    //            }
    //        }
    //
    //        if (!is_null($this->bui?->getId())) {
    //            if (isset($this->formValues['enterpriseClient']) && '' !== $this->formValues['enterpriseClient']) {
    //                /** @var int $enterpriseClient */
    //                $enterpriseClient = $this->formValues['enterpriseClient'];
    //                $this->enterpriseClient = $enterpriseClient;
    //            }
    //        } else {
    //            if (isset($this->formValues['enterpriseClient']) && '' !== $this->formValues['enterpriseClient']) {
    //                /** @var int $enterpriseClient */
    //                $enterpriseClient = $this->formValues['enterpriseClient'];
    //                if ((int) $this->enterpriseClient > $enterpriseClient) {
    //                    $this->formValues['enterpriseClient'] = (string) $this->enterpriseClient;
    //                    $this->formValues['individualClient'] = '0';
    //                    $this->individualClient = 0;
    //                } else {
    //                    $this->enterpriseClient = $enterpriseClient;
    //                }
    //            }
    //
    //            if (0 !== $this->enterpriseClient && '' === $this->formValues['enterpriseClient']) {
    //                $this->formValues['enterpriseClient'] = (string) $this->enterpriseClient;
    //            }
    //        }
    //    }

    public function preValue(): void
    {
        $this->applyIntegerField('constructor');
        $this->applyIntegerField('corporateEntity');
        $this->applyProject();
        $this->applyPriceField('estimatedValueUrbanization', $this->urbanizationEstimateTotalPrice);
        $this->applyPriceField('projectPriceTechnicalPreparation', $this->ptpEstimateTotalPrice);
        $this->applyPriceField('estimatedJustValue', $this->justValueEstimateTotalPrice);
        $this->applyClientField('individualClient', $this->individualClient);
        $this->applyEnterpriseClientField();
    }

    private function applyIntegerField(string $field): void
    {
        $value = $this->{$field};

        if (0 === $value) {
            return;
        }

        $this->formValues[$field] = $value;
        $this->{$field} = 0;
    }

    private function applyProject(): void
    {
        if (0 === $this->project) {
            return;
        }

        $project = $this->projectRepository->find((int) $this->project);
        $this->bui?->setProject($project);
    }

    private function applyPriceField(string $formKey, ?float $value = null): void
    {
        if (0.0 === $value) {
            return;
        }

        $this->formValues[$formKey] = (float) $value / 100;
    }

    private function applyClientField(string $field, ?int $currentValue = null): void
    {
        $formValue = $this->formValues[$field] ?? '';

        if ('' === $formValue) {
            return;
        }

        /** @var int $incomingValue */
        $incomingValue = $formValue;

        if (!is_null($this->bui?->getId())) {
            $this->{$field} = $incomingValue;

            return;
        }

        if ($currentValue > $incomingValue) {
            $this->formValues[$field] = (string) $currentValue;
        } else {
            $this->{$field} = $incomingValue;
        }
    }

    private function applyEnterpriseClientField(): void
    {
        $formValue = $this->formValues['enterpriseClient'] ?? '';

        if (!is_null($this->bui?->getId())) {
            if ('' !== $formValue) {
                /** @var int $enterpriseClient */
                $enterpriseClient = $formValue;
                $this->enterpriseClient = $enterpriseClient;
            }

            return;
        }

        if ('' !== $formValue) {
            /** @var int $enterpriseClient */
            $enterpriseClient = $formValue;

            if ((int) $this->enterpriseClient > $enterpriseClient) {
                $this->formValues['enterpriseClient'] = (string) $this->enterpriseClient;
                $this->formValues['individualClient'] = '0';
                $this->individualClient = 0;
            } else {
                $this->enterpriseClient = $enterpriseClient;
            }
        }

        if (0 !== $this->enterpriseClient && '' === $formValue) {
            $this->formValues['enterpriseClient'] = (string) $this->enterpriseClient;
        }
    }

    /**
     * @return FormInterface<Building>
     */
    protected function instantiateForm(): FormInterface
    {
        $this->preValue();

        return $this->createForm(BuildingType::class, $this->bui, [
            'screen' => 'building',
            'urbanizationEstimate' => $this->urbanizationEstimateTotalPrice,
            'ptpEstimate' => $this->ptpEstimateTotalPrice,
            'justValueEstimate' => $this->justValueEstimateTotalPrice,
        ]);
    }

    #[LiveAction]
    //    public function save(
    //        BuildingRepository $buildingRepository,
    //        //        ConstructorRepository $constructorRepository,
    //        CorporateEntityRepository $corporateEntityRepository,
    //        ProjectRepository $projectRepository,
    //        DraftsmanRepository $draftsmanRepository,
    //        ClientRepository $clientRepository,
    //    ): ?Response {
    //        $this->preValue();
    //
    //        $successMsg = (is_null($this->bui?->getId())) ? 'Se ha agregado la obra.' : 'Se ha modificado la obra.';
    //
    //        $this->submitForm();
    //
    //        if ($this->isSubmitAndValid()) {
    //            /** @var Building $building */
    //            $building = $this->getForm()->getData();
    //
    //            $client = 0;
    //            if ('individual' === $this->formValues['clientType']) {
    //                /** @var int $client */
    //                $client = $this->formValues['individualClient'];
    //            }
    //
    //            if ('enterprise' === $this->formValues['clientType']) {
    //                /** @var int $client */
    //                $client = $this->formValues['enterpriseClient'];
    //            }
    //
    //            $client = $clientRepository->find($client);
    //            $building->setClient($client);
    //
    //            //            if (false !== (bool) $this->formValues['constructor']) {
    //            //                $constructor = $constructorRepository->find($this->formValues['constructor']);
    //            //                if (null !== $constructor) {
    //            //                    $building->addConstructor($constructor);
    //            //                }
    //            //            }
    //
    //            if (false !== (bool) $this->formValues['corporateEntity']) {
    //                $corporateEntity = $corporateEntityRepository->find($this->formValues['corporateEntity']);
    //                if (null !== $corporateEntity) {
    //                    $building->addCorporateEntity($corporateEntity);
    //                }
    //            }
    //
    //            if (0 !== $this->project) {
    //                $project = $projectRepository->find((int) $this->project);
    //                assert($project instanceof Project);
    //                $building->setProject($project);
    //            }
    //
    //            if (isset($this->formValues['draftsman']) && false !== (bool) $this->formValues['draftsman']) {
    //                $draftsman = $draftsmanRepository->find($this->formValues['draftsman']);
    //                if (null !== $draftsman) {
    //                    $building->addDraftsman($draftsman);
    //                }
    //            }
    //
    //            $buildingRepository->save($building, true);
    //
    //            $this->bui = new Building();
    //            if (!is_null($this->modal)) {
    //                $this->modalManage($building, 'Se ha seleccionado la nueva obra agregada.', [
    //                    'building' => $building->getId(),
    //                ]);
    //
    //                return null;
    //            }
    //
    //            if ($this->ajax) {
    //                $this->ajaxManage($building, $successMsg);
    //
    //                return null;
    //            }
    //
    //            $this->addFlash('success', $successMsg);
    //
    //            return $this->redirectToRoute('app_building_edit', ['id' => $building->getId(), 'project' => $building->getProject()?->getId()], Response::HTTP_SEE_OTHER);
    //        }
    //
    //        return null;
    //    }

    public function save(
        BuildingRepository $buildingRepository,
        CorporateEntityRepository $corporateEntityRepository,
        ProjectRepository $projectRepository,
        DraftsmanRepository $draftsmanRepository,
        ClientRepository $clientRepository,
        SeparateConceptRepository $separateConceptRepository,
    ): ?Response {
        $this->preValue();

        $successMsg = is_null($this->bui?->getId())
            ? 'Se ha agregado la obra.'
            : 'Se ha modificado la obra.';

        $this->submitForm();

        if (!$this->isSubmitAndValid()) {
            return null;
        }

        /** @var Building $building */
        $building = $this->getForm()->getData();

        $this->applyClient($building, $clientRepository);
        $this->applyCorporateEntity($building, $corporateEntityRepository);
        $this->applyProjectToBuilding($building, $projectRepository);
        $this->applyDraftsman($building, $draftsmanRepository);
        if (null === $building->getId()) {
            $this->addSeparateConcepts($separateConceptRepository, $building);
        }

        $buildingRepository->save($building, true);

        $this->bui = new Building();

        return $this->resolveResponse($building, $successMsg);
    }

    private function applyClient(Building $building, ClientRepository $clientRepository): void
    {
        $clientId = match ($this->formValues['clientType']) {
            'individual' => $this->formValues['individualClient'],
            'enterprise' => $this->formValues['enterpriseClient'],
            default => 0,
        };

        $building->setClient($clientRepository->find($clientId));
    }

    private function applyCorporateEntity(
        Building $building,
        CorporateEntityRepository $corporateEntityRepository,
    ): void {
        if (false === (bool) ($this->formValues['corporateEntity'] ?? false)) {
            return;
        }

        $corporateEntity = $corporateEntityRepository->find($this->formValues['corporateEntity']);

        if (null !== $corporateEntity) {
            $building->addCorporateEntity($corporateEntity);
        }
    }

    private function applyProjectToBuilding(
        Building $building,
        ProjectRepository $projectRepository,
    ): void {
        if (0 === $this->project) {
            return;
        }

        $project = $projectRepository->find((int) $this->project);
        assert($project instanceof Project);
        $building->setProject($project);
    }

    private function applyDraftsman(
        Building $building,
        DraftsmanRepository $draftsmanRepository,
    ): void {
        if (!isset($this->formValues['draftsman']) || false === (bool) $this->formValues['draftsman']) {
            return;
        }

        $draftsman = $draftsmanRepository->find($this->formValues['draftsman']);

        if (null !== $draftsman) {
            $building->addDraftsman($draftsman);
        }
    }

    private function resolveResponse(Building $building, string $successMsg): ?Response
    {
        if (!is_null($this->modal)) {
            $this->modalManage($building, 'Se ha seleccionado la nueva obra agregada.', [
                'building' => $building->getId(),
            ]);

            return null;
        }

        if ($this->ajax) {
            $this->ajaxManage($building, $successMsg);

            return null;
        }

        $this->addFlash('success', $successMsg);

        return $this->redirectToRoute(
            'app_building_edit',
            [
                'id' => $building->getId(),
                'project' => $building->getProject()?->getId(),
            ],
            Response::HTTP_SEE_OTHER
        );
    }

    /** @SuppressWarnings(PHPMD.UnusedPrivateMethod) */
    private function getDataModelValue(): string
    {
        return 'norender|*';
    }

    public function isIndividualClient(): bool
    {
        if (is_null($this->bui)) {
            return false;
        }

        return $this->bui->isIndividualClient($this->individualClientRepository);
    }

    public function isEnterpriseClient(): bool
    {
        if (is_null($this->bui)) {
            return false;
        }

        return $this->bui->isEnterpriseClient($this->enterpriseClientRepository);
    }

    public function getRangeMinPrice(): int|float
    {
        assert($this->bui instanceof Building);

        return $this->buildingValuationService->getRangeMinPrice($this->bui);
    }

    public function getRangeMaxPrice(): int|float
    {
        assert($this->bui instanceof Building);

        return $this->buildingValuationService->getRangeMaxPrice($this->bui);
    }

    public function getResultIte(): float
    {
        assert($this->bui instanceof Building);

        return $this->buildingValuationService->getResultIte($this->bui);
    }

    private function addSeparateConcepts(SeparateConceptRepository $separateConceptRepository, Building $building): void
    {
        $separateConcepts = $separateConceptRepository->findBy([], ['number' => 'ASC']);
        foreach ($separateConcepts as $separateConcept) {
            $percent = (bool) $separateConcept->getPercent() ? $separateConcept->getPercent() : 0;

            $buildingSeparateConcept = new BuildingSeparateConcept();
            $buildingSeparateConcept->setBuilding($building);
            $buildingSeparateConcept->setSeparateConcept($separateConcept);
            $buildingSeparateConcept->setPercentEstimatedAdjustValue($percent);
            $buildingSeparateConcept->setPercentEstimatedToExecuteValue($percent);
            $buildingSeparateConcept->setPercentRealValue($percent);

            $building->addBuildingSeparateConcept($buildingSeparateConcept);
            //            $manager->persist($buildingSeparateConcept);
        }
    }
}
