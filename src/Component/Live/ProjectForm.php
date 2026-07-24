<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Building;
use App\Entity\Contract;
use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ClientRepository;
use App\Repository\CurrencyRepository;
use App\Repository\EnterpriseClientRepository;
use App\Repository\IndividualClientRepository;
use App\Repository\InvestmentRepository;
use App\Repository\PlannerRepository;
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
    public ?int $currency = 0;

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
        $this->applyIntegerField('investment');
        $this->applyIntegerField('currency');
        $this->applyClientField('individualClient', $this->individualClient);
        $this->applyEnterpriseClientField();
    }

    private function applyIntegerField(string $field): void
    {
        if (0 === $this->{$field}) {
            return;
        }

        $this->formValues[$field] = $this->{$field};
        $this->{$field} = 0;
    }

    private function isExistingEntity(): bool
    {
        return !is_null($this->pro?->getId());
    }

    private function applyClientField(string $field, ?int $currentValue = null): void
    {
        $formValue = $this->formValues[$field] ?? '';

        if ('' === $formValue) {
            return;
        }

        /** @var int $incomingValue */
        $incomingValue = $formValue;

        if ($this->isExistingEntity()) {
            $this->{$field} = $incomingValue;

            return;
        }

        if ((int) $currentValue > $incomingValue) {
            $this->formValues[$field] = (string) $currentValue;
        } else {
            $this->{$field} = $incomingValue;
        }
    }

    private function applyEnterpriseClientField(): void
    {
        $formValue = $this->formValues['enterpriseClient'] ?? '';

        if ($this->isExistingEntity()) {
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
        PlannerRepository $plannerRepository,
        CurrencyRepository $currencyRepository,
    ): ?Response {
        $this->preValue();

        $successMsg = is_null($this->pro?->getId())
            ? 'Se ha agregado el proyecto.'
            : 'Se ha modificado el proyecto.';

        $this->submitForm();

        if (!$this->isSubmitAndValid()) {
            return null;
        }

        /** @var Project $project */
        $project = $this->getForm()->getData();

        $this->applyCurrency($project, $currencyRepository);
        $this->applyInvestment($project, $investmentRepository);
        $this->applyClient($project, $clientRepository);
        $this->applyDraftsman($project, $plannerRepository);
        $this->applyContract($project);

        $projectRepository->save($project, true);

        $this->pro = new Project();

        return $this->resolveResponse($project, $successMsg);
    }

    private function applyInvestment(Project $project, InvestmentRepository $investmentRepository): void
    {
        $investment = $investmentRepository->find($this->formValues['investment']);
        $project->setInvestment($investment);
    }

    private function applyCurrency(Project $project, CurrencyRepository $currencyRepository): void
    {
        $currency = $currencyRepository->find($this->formValues['currency']);
        $project->setCurrency($currency);
    }

    private function applyClient(Project $project, ClientRepository $clientRepository): void
    {
        $clientId = match ($this->formValues['clientType']) {
            'individual' => $this->formValues['individualClient'],
            'enterprise' => $this->formValues['enterpriseClient'],
            default => 0,
        };

        $project->setClient($clientRepository->find($clientId));
    }

    private function applyDraftsman(Project $project, PlannerRepository $plannerRepository): void
    {
        $plannerValue = $this->formValues['planner'] ?? '';

        if ('' === $plannerValue) {
            return;
        }

        $planner = $plannerRepository->find($plannerValue);

        if (null !== $planner) {
            $project->addPlanner($planner);
        }
    }

    private function applyContract(Project $project): void
    {
        /** @var array<string, array<string, mixed>|null> $formValues */
        $formValues = $this->formValues;

        if (null === $this->pro?->getId()) {
            $this->applyContractForNew($project, $formValues);

            return;
        }

        $this->applyContractForExisting($project, $formValues);
    }

    /**
     * @param array<mixed> $formValues
     */
    private function applyContractForNew(Project $project, array $formValues): void
    {
        /** @var array<string, mixed>|null $contract */
        $contract = $formValues['contract'] ?? null;

        if (null !== $contract && '' === ($contract['code'] ?? '')) {
            $this->formValues['contract'] = null;
            $project->setContract(null);
        }
    }

    /**
     * @param array<mixed> $formValues
     */
    private function applyContractForExisting(Project $project, array $formValues): void
    {
        /** @var array<string, mixed>|null $contract */
        $contract = $formValues['contract'] ?? null;

        if (!is_array($contract) || '' !== ($contract['code'] ?? '')) {
            return;
        }

        /** @var array<string, mixed> $updatedContract */
        $updatedContract = $this->formValues['contract'] ?? [];
        $updatedContract['code'] = $this->pro?->getContract()?->getCode();
        $updatedContract['year'] = $this->pro?->getContract()?->getYear();

        $this->formValues['contract'] = $updatedContract;
        $project->setContract($this->contract);
    }

    private function resolveResponse(Project $project, string $successMsg): ?Response
    {
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

        return $this->redirectToRoute(
            'app_building_project',
            ['project' => $project->getId()],
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
