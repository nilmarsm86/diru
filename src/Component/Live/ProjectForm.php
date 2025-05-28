<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Component\Twig\Modal\Modal;
use App\Entity\Building;
use App\Entity\Constructor;
use App\Entity\Project;
use App\Entity\Province;
use App\Form\BuildingType;
use App\Form\ConstructorType;
use App\Form\ProjectType;
use App\Form\ProvinceType;
use App\Repository\BuildingRepository;
use App\Repository\ClientRepository;
use App\Repository\ConstructorRepository;
use App\Repository\InvestmentRepository;
use App\Repository\ProjectRepository;
use App\Repository\ProvinceRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'component/live/project_form.html.twig')]
final class ProjectForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

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

    public function mount(?Project $pro = null): void
    {
        $this->pro = (is_null($pro)) ? new Project() : $pro;
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
    public function save(ProjectRepository $projectRepository, ClientRepository $clientRepository, InvestmentRepository $investmentRepository): ?Response
    {
        $this->preValue();
        $successMsg = (is_null($this->pro->getId())) ? 'Se ha agregado el proyecto.' : 'Se ha modificado el proyecto.';

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var Project $project */
            $project = $this->getForm()->getData();

            $investment = $investmentRepository->find((int)$this->formValues['investment']);
            $project->setInvestment($investment);

            $client = $clientRepository->find((int)$this->formValues['client']);
            $project->setClient($client);

            $projectRepository->save($project, true);

            $this->pro = new Project();
            if (!is_null($this->modal)) {
                $this->modalManage($project, 'Seleccione el nuevo proyecto agregada.', [
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

}
