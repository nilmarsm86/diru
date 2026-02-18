<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Building;
use App\Entity\Project;
use App\Form\BuildingType;
use App\Repository\BuildingRepository;
use App\Repository\ClientRepository;
use App\Repository\ConstructorRepository;
use App\Repository\CorporateEntityRepository;
use App\Repository\DraftsmanRepository;
use App\Repository\EnterpriseClientRepository;
use App\Repository\IndividualClientRepository;
use App\Repository\ProjectRepository;
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
    public ?int $client = 0;

    #[LiveProp(writable: true)]
    public ?int $individualClient = 0;

    #[LiveProp(writable: true)]
    public ?int $enterpriseClient = 0;

    public function __construct(
        private readonly ProjectRepository $projectRepository,
        private readonly IndividualClientRepository $individualClientRepository,
        private readonly EnterpriseClientRepository $enterpriseClientRepository,
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

    public function preValue(): void
    {
        if (0 !== $this->constructor) {
            $this->formValues['constructor'] = (string) $this->constructor;
            $this->constructor = 0;
        }

        if (0 !== $this->corporateEntity) {
            $this->formValues['corporateEntity'] = (string) $this->corporateEntity;
            $this->corporateEntity = 0;
        }

        if (0 !== $this->project) {
            $project = $this->projectRepository->find((int) $this->project);
            $this->bui?->setProject($project);
        }

        if (0.0 !== $this->urbanizationEstimateTotalPrice) {
            $this->formValues['estimatedValueUrbanization'] = (float) $this->urbanizationEstimateTotalPrice / 100;
        }

        if (0.0 !== $this->ptpEstimateTotalPrice) {
            $this->formValues['projectPriceTechnicalPreparation'] = (float) $this->ptpEstimateTotalPrice / 100;
        }

        if (!is_null($this->bui?->getId())) {
            if (isset($this->formValues['individualClient']) && '' !== $this->formValues['individualClient']) {
                /** @var int $individualClient */
                $individualClient = $this->formValues['individualClient'];
                $this->individualClient = $individualClient;
            }
        } else {
            if (isset($this->formValues['individualClient']) && '' !== $this->formValues['individualClient']) {
                /** @var int $individualClient */
                $individualClient = $this->formValues['individualClient'];
                if ((int) $this->individualClient > $individualClient) {
                    $this->formValues['individualClient'] = (string) $this->individualClient;
                } else {
                    $this->individualClient = $individualClient;
                }
            }
        }

        if (!is_null($this->bui?->getId())) {
            if (isset($this->formValues['enterpriseClient']) && '' !== $this->formValues['enterpriseClient']) {
                /** @var int $enterpriseClient */
                $enterpriseClient = $this->formValues['enterpriseClient'];
                $this->enterpriseClient = $enterpriseClient;
            }
        } else {
            if (isset($this->formValues['enterpriseClient']) && '' !== $this->formValues['enterpriseClient']) {
                /** @var int $enterpriseClient */
                $enterpriseClient = $this->formValues['enterpriseClient'];
                if ((int) $this->enterpriseClient > $enterpriseClient) {
                    $this->formValues['enterpriseClient'] = (string) $this->enterpriseClient;
                    $this->formValues['individualClient'] = '0';
                    $this->individualClient = 0;
                } else {
                    $this->enterpriseClient = $enterpriseClient;
                }
            }

            if (0 !== $this->enterpriseClient && '' === $this->formValues['enterpriseClient']) {
                $this->formValues['enterpriseClient'] = (string) $this->enterpriseClient;
            }
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
        ]);
    }

    #[LiveAction]
    public function save(
        BuildingRepository $buildingRepository,
        ConstructorRepository $constructorRepository,
        CorporateEntityRepository $corporateEntityRepository,
        ProjectRepository $projectRepository,
        DraftsmanRepository $draftsmanRepository,
        ClientRepository $clientRepository,
    ): ?Response {
        $this->preValue();

        $successMsg = (is_null($this->bui?->getId())) ? 'Se ha agregado la obra.' : 'Se ha modificado la obra.';

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var Building $building */
            $building = $this->getForm()->getData();

            $client = 0;
            if ('individual' === $this->formValues['clientType']) {
                /** @var int $client */
                $client = $this->formValues['individualClient'];
            }

            if ('enterprise' === $this->formValues['clientType']) {
                /** @var int $client */
                $client = $this->formValues['enterpriseClient'];
            }

            $client = $clientRepository->find($client);
            $building->setClient($client);

            if (false !== (bool) $this->formValues['constructor']) {
                $constructor = $constructorRepository->find($this->formValues['constructor']);
                if (null !== $constructor) {
                    $building->addConstructor($constructor);
                }
            }

            if (false !== (bool) $this->formValues['corporateEntity']) {
                $corporateEntity = $corporateEntityRepository->find($this->formValues['corporateEntity']);
                if (null !== $corporateEntity) {
                    $building->addCorporateEntity($corporateEntity);
                }
            }

            if (0 !== $this->project) {
                $project = $projectRepository->find((int) $this->project);
                assert($project instanceof Project);
                $building->setProject($project);
            }

            if (isset($this->formValues['draftsman']) && false !== (bool) $this->formValues['draftsman']) {
                $draftsman = $draftsmanRepository->find($this->formValues['draftsman']);
                if (null !== $draftsman) {
                    $building->addDraftsman($draftsman);
                }
            }

            $buildingRepository->save($building, true);

            $this->bui = new Building();
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

            return $this->redirectToRoute('app_building_edit', ['id' => $building->getId(), 'project' => $building->getProject()?->getId()], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

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
}
