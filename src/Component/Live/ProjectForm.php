<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Building;
use App\Entity\Contract;
use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ClientRepository;
use App\Repository\DraftsmanRepository;
use App\Repository\EnterpriseClientRepository;
use App\Repository\IndividualClientRepository;
use App\Repository\InvestmentRepository;
use App\Repository\ProjectRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
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

    #[LiveProp]
    public ?Contract $contract = null;

    public function __construct(
        private readonly IndividualClientRepository $individualClientRepository,
        private readonly EnterpriseClientRepository $enterpriseClientRepository,
        private readonly RouterInterface            $router
    )
    {

    }

    public function mount(?Project $pro = null): void
    {
        $this->pro = (is_null($pro)) ? new Project() : $pro;
        $this->contract = $this->pro->getContract();
    }

    /**
     * @return void
     */
    public function preValue(): void
    {
        if ($this->investment !== 0) {
            $this->formValues['investment'] = (string)$this->investment;
            $this->investment = 0;
        }

        if ($this->client !== 0) {
            $this->formValues['client'] = (string)$this->client;
            $this->client = 0;
        }
    }

    protected function instantiateForm(): FormInterface
    {
        $this->preValue();
        return $this->createForm(ProjectType::class, $this->pro);
    }

    /**
     * @throws Exception
     */
    #[LiveAction]
    public function save(
        ProjectRepository    $projectRepository,
        ClientRepository     $clientRepository,
        InvestmentRepository $investmentRepository,
        Security             $security,
        DraftsmanRepository  $draftsmanRepository
    ): ?Response
    {
        $this->preValue();
        $successMsg = (is_null($this->pro->getId())) ? 'Se ha agregado el proyecto.' : 'Se ha modificado el proyecto.';

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var Project $project */
            $project = $this->getForm()->getData();

            $investment = $investmentRepository->find((int)$this->formValues['investment']);
            $project->setInvestment($investment);

            if ($this->formValues['clientType'] === 'individual') {
                $client = (int)$this->formValues['individualClient'];
            }

            if ($this->formValues['clientType'] === 'enterprise') {
                $client = (int)$this->formValues['enterpriseClient'];
            }

            $client = $clientRepository->find((int)$client);
            $project->setClient($client);

            if (!empty($this->formValues['draftsman'])) {
                $draftsman = $draftsmanRepository->find($this->formValues['draftsman']);

                if (is_null($project->getId())) {
                    /** @var Building $building */
                    foreach ($this->getForm()->get('buildings')->getData() as $building) {
                        $building->addDraftsman($draftsman);
                    }
                }
            }

            if (is_null($this->pro->getId())) {
                if ($this->formValues['contract'] && empty($this->formValues['contract']['code'])) {
                    $this->formValues['contract'] = null;
                    $project->setContract(null);
                }
            } else {
                if ($this->formValues['contract'] && empty($this->formValues['contract']['code'])) {
                    $this->formValues['contract']['code'] = $this->pro->getContract()->getCode();
                    $this->formValues['contract']['year'] = $this->pro->getContract()->getYear();
                    $project->setContract($this->contract);
                }

                //Change draftmans
                foreach ($this->getForm()->get('buildings')->getData() as $key => $building) {
                    if(isset($this->formValues['buildings'][$key]['draftsman'])){
                        $draftsman = $draftsmanRepository->find($this->formValues['buildings'][$key]['draftsman']);
                        if ($draftsman) {
                            $building->addDraftsman($draftsman);
                        }
                    }
                }
            }

            $projectRepository->save($project, true);

            $this->pro = new Project();
            if (!is_null($this->modal)) {
                $this->modalManage($project, 'Se ha seleccionado el nuevo proyecto agregado.', [
                    'project' => $project->getId()
                ]);
                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($project, $successMsg);
                return null;
            }

            $this->addFlash('success', $successMsg);
            return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): ?string
    {
        return 'norender|*';
    }

    public function isIndividualClient(): bool
    {
        return $this->pro->isIndividualClient($this->individualClientRepository);
    }

    public function isEnterpriseClient(): bool
    {
        return $this->pro->isEnterpriseClient($this->enterpriseClientRepository);
    }

    public function isNew(): bool
    {
        return is_null($this->pro->getId());
    }

    public function getUrl(Building $building): string
    {
        if (is_null($building->getLand())) {
            return $this->router->generate('app_land_new', ['modal' => 'modal-load', 'building' => $building->getId()]);
        } else {
            return $this->router->generate('app_land_edit', ['modal' => 'modal-load', 'building' => $building->getId(), 'id' => $building->getLand()->getId()]);
        }
    }

}
