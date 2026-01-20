<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Project;
use App\Entity\ProjectUrbanRegulation;
use App\Entity\UrbanRegulation;
use App\Form\ProjectUrbanRegulationType;
use App\Repository\ProjectRepository;
use App\Repository\ProjectUrbanRegulationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'component/live/project_urban_regulation_form.html.twig')]
final class ProjectUrbanRegulationForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?ProjectUrbanRegulation $pur = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp]
    public ProjectUrbanRegulation $entity;

    #[LiveProp(writable: true)]
    public ?int $project = 0;

    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    public function mount(?ProjectUrbanRegulation $pur = null): void
    {
        $this->pur = (is_null($pur)) ? new ProjectUrbanRegulation() : $pur;
        $this->entity = $this->pur;
    }

    public function preValue(): void
    {
        if (0 !== $this->project) {
            $project = $this->projectRepository->find((int) $this->project);
            $this->pur?->setProject($project);
        }
    }

    /**
     * @return FormInterface<UrbanRegulation>
     */
    protected function instantiateForm(): FormInterface
    {
        $this->preValue();

        return $this->createForm(ProjectUrbanRegulationType::class, $this->pur);
    }

    #[LiveAction]
    public function save(ProjectUrbanRegulationRepository $projectUrbanRegulationRepository): ?Response
    {
        $this->preValue();

        $successMsg = (is_null($this->pur?->getId())) ? 'Se ha agregado la regulación al proyecto.' : 'Se ha modificado la regulación del proyecto.'; // TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var ProjectUrbanRegulation $pur */
            $pur = $this->getForm()->getData();

            if (0 !== $this->project) {
                $project = $this->projectRepository->find((int) $this->project);
                assert($project instanceof Project);
                $pur->setProject($project);
            }

            $projectUrbanRegulationRepository->save($pur, true);

            $this->pur = new ProjectUrbanRegulation();
            $this->entity = $this->pur;
            if (!is_null($this->modal)) {
                $this->modalManage($pur, 'Se ha seleccionado la regulación del proyecto.', [
                    'projectUrbanRegulation' => $pur->getId(),
                ]);

                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($pur, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('app_project_urban_regulation_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): string
    {
        return 'norender|*';
    }
}
