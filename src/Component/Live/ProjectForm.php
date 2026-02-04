<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Building;
use App\Entity\Contract;
use App\Entity\Draftsman;
use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ClientRepository;
use App\Repository\ConstructorRepository;
use App\Repository\CorporateEntityRepository;
use App\Repository\DraftsmanRepository;
use App\Repository\EnterpriseClientRepository;
use App\Repository\IndividualClientRepository;
use App\Repository\InvestmentRepository;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent(template: 'component/live/project_form.html.twig')]
final class ProjectForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentToolsTrait;
    use ComponentForm;
    use LiveCollectionTrait;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?Project $pro = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp(writable: true)]
    public ?int $investment = 0;

    #[LiveProp(writable: true)]
    public ?int $client = 0;

    #[LiveProp(writable: true)]
    public ?int $individualClient = 0;

    #[LiveProp(writable: true)]
    public ?int $enterpriseClient = 0;

    #[LiveProp]
    public ?Contract $contract = null;

    public function __construct(
        private readonly IndividualClientRepository $individualClientRepository,
        private readonly EnterpriseClientRepository $enterpriseClientRepository,
        private readonly RouterInterface $router,
    ) {
    }

    public function mount(?Project $pro = null): void
    {
        $this->pro = (is_null($pro)) ? new Project() : $pro;
        $this->contract = $this->pro->getContract();
    }

    public function preValue(): void
    {
        if (0 !== $this->investment) {
            $this->formValues['investment'] = (string) $this->investment;
            $this->investment = 0;
        }

        if (!is_null($this->pro?->getId())) {
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

        if (!is_null($this->pro?->getId())) {
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
     * @return FormInterface<Project>
     */
    protected function instantiateForm(): FormInterface
    {
        $this->preValue();

        return $this->createForm(ProjectType::class, $this->pro);
    }

    #[LiveAction]
    public function save(
        ProjectRepository $projectRepository,
        ClientRepository $clientRepository,
        InvestmentRepository $investmentRepository,
        DraftsmanRepository $draftsmanRepository,
        ConstructorRepository $constructorRepository,
        CorporateEntityRepository $corporateEntityRepository,
    ): ?Response {
        $this->preValue();
        $successMsg = (is_null($this->pro?->getId())) ? 'Se ha agregado el proyecto.' : 'Se ha modificado el proyecto.';

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var Project $project */
            $project = $this->getForm()->getData();

            $investment = $investmentRepository->find($this->formValues['investment']);
            $project->setInvestment($investment);

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
            $project->setClient($client);

            if (isset($this->formValues['draftsman']) && '' !== $this->formValues['draftsman']) {
                $draftsman = $draftsmanRepository->find($this->formValues['draftsman']);

                if (is_null($project->getId())) {
                    /** @var Building[] $data */
                    $data = $this->getForm()->get('buildings')->getData();
                    foreach ($data as $building) {
                        assert($draftsman instanceof Draftsman);
                        $building->addDraftsman($draftsman);
                    }
                }
            }

            /** @var array<string, array<string, mixed>|null> $formValues */
            $formValues = $this->formValues;
            if (null === $this->pro?->getId()) {
                /** @var array<string, mixed>|null $contract */
                $contract = $formValues['contract'] ?? null;
                if (null !== $contract && ($contract['code'] ?? '') === '') {
                    $formValues['contract'] = null;
                    $project->setContract(null);
                }

                $this->formValues = $formValues;
            } else {
                /** @var array<string, mixed>|null $contract */
                $contract = $formValues['contract'] ?? null;
                if (is_array($contract) && ($contract['code'] ?? '') === '') {
                    $formValues['contract']['code'] = $this->pro->getContract()?->getCode();
                    $formValues['contract']['year'] = $this->pro->getContract()?->getYear();
                    $project->setContract($this->contract);
                }

                // Change draftmans
                /** @var Building[] $data */
                $data = $this->getForm()->get('buildings')->getData();
                /** @var array<string, array<string, array<string, mixed>>> $fv */
                $fv = $this->formValues;
                foreach ($data as $key => $building) {
                    if (isset($fv['buildings'][$key]['draftsman'])) {
                        $draftsman = $draftsmanRepository->find($fv['buildings'][$key]['draftsman']);
                        if (null !== $draftsman) {
                            $building->addDraftsman($draftsman);
                        }
                    }
                }

                $this->formValues = $formValues;
            }

            // fix constructor
            /** @var Building[] $data */
            $data = $this->getForm()->get('buildings')->getData();

            /** @var array<string, array<string, array<string, mixed>>> $fv */
            $fv = $this->formValues;

            // constructor
            foreach ($data as $key => $building) {
                if (isset($fv['buildings'][$key]['constructor'])) {
                    $constructor = $constructorRepository->find($fv['buildings'][$key]['constructor']);
                    if (null !== $constructor) {
                        $building->addConstructor($constructor);
                    }
                }
            }

            // corpoate entities
            foreach ($data as $key => $building) {
                if (isset($fv['buildings'][$key]['corporateEntity'])) {
                    $corporateEntity = $corporateEntityRepository->find($fv['buildings'][$key]['corporateEntity']);
                    if (null !== $corporateEntity) {
                        $building->addCorporateEntity($corporateEntity);
                    }
                }
            }

            $projectRepository->save($project, true);

            $this->pro = new Project();
            if (!is_null($this->modal)) {
                $this->modalManage($project, 'Se ha seleccionado el nuevo proyecto agregado.', [
                    'project' => $project->getId(),
                ]);

                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($project, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('app_building_project', ['project' => $project->getId()], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): string
    {
        return 'norender|*';
    }

    public function isIndividualClient(): bool
    {
        if (is_null($this->pro)) {
            return false;
        }

        return $this->pro->isIndividualClient($this->individualClientRepository);
    }

    public function isEnterpriseClient(): bool
    {
        if (is_null($this->pro)) {
            return false;
        }

        return $this->pro->isEnterpriseClient($this->enterpriseClientRepository);
    }

    public function isNew(): bool
    {
        return is_null($this->pro?->getId());
    }

    public function getUrl(Building $building): string
    {
        if (is_null($building->getLand())) {
            return $this->router->generate('app_land_new', ['modal' => 'modal-load', 'building' => $building->getId()]);
        }

        return $this->router->generate('app_land_edit', ['modal' => 'modal-load', 'building' => $building->getId(), 'id' => $building->getLand()->getId()]);
    }
}
