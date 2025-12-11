<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Building;
use App\Entity\ProjectTechnicalPreparationEstimate;
use App\Form\ProjectTechnicalPreparationEstimateType;
use App\Repository\ProjectTechnicalPreparationEstimateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'component/live/ptp_estimate_form.html.twig')]
final class ProjectTechnicalPreparationEstimateForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?ProjectTechnicalPreparationEstimate $ptpe = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp]
    public ProjectTechnicalPreparationEstimate $entity;

    #[LiveProp]
    public ?Building $building = null;

    public function mount(?ProjectTechnicalPreparationEstimate $ue = null, ?Building $building = null): void
    {
        $this->ptpe = (is_null($ue)) ? new ProjectTechnicalPreparationEstimate() : $ue;
        $this->entity = $this->ptpe;
        $this->building = $building;
        $this->building?->addProjectTechnicalPreparationEstimate($this->ptpe);
    }

    /**
     * @return FormInterface<ProjectTechnicalPreparationEstimate>
     */
    protected function instantiateForm(): FormInterface
    {
        assert($this->ptpe instanceof ProjectTechnicalPreparationEstimate);
        $this->building?->addProjectTechnicalPreparationEstimate($this->ptpe);

        return $this->createForm(ProjectTechnicalPreparationEstimateType::class, $this->ptpe);
    }

    /**
     * @throws \Exception
     */
    #[LiveAction]
    public function save(ProjectTechnicalPreparationEstimateRepository $projectTechnicalPreparationEstimateRepository): ?Response
    {
        $successMsg = (is_null($this->ptpe?->getId())) ? 'Se ha agregado el estimado de proyecto y preparación técnica.' : 'Se ha modificado el estimado de proyecto y preparación técnica.'; // TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var ProjectTechnicalPreparationEstimate $ue */
            $ue = $this->getForm()->getData();
            $this->building?->addProjectTechnicalPreparationEstimate($ue);

            $projectTechnicalPreparationEstimateRepository->save($ue, true);

            $this->ptpe = new ProjectTechnicalPreparationEstimate();
            $this->entity = $this->ptpe;
            if (!is_null($this->modal)) {
                $this->modalManage($ue, 'Se ha sumado el precio del nuevo estimado de proyecto y preparación técnica.', [
                    'ptpEstimateTotalPrice' => $ue->getBuilding()?->getProjectTechnicalPreparationEstimateTotalPrice(),
                ]);

                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($ue, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('app_ptp_estimate_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): string
    {
        return 'norender|*';
    }
}
