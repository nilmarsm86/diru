<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Building;
use App\Entity\UrbanizationEstimate;
use App\Form\UrbanizationEstimateType;
use App\Repository\UrbanizationEstimateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'component/live/urbanization_estimate_form.html.twig')]
final class UrbanizationEstimateForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?UrbanizationEstimate $ue = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp]
    public UrbanizationEstimate $entity;

    #[LiveProp]
    public ?Building $building = null;

    public function mount(?UrbanizationEstimate $ue = null, ?Building $building = null): void
    {
        $this->ue = (is_null($ue)) ? new UrbanizationEstimate() : $ue;
        $this->entity = $this->ue;
        $this->building = $building;
        $this->building?->addUrbanizationEstimate($this->ue);
    }

    protected function instantiateForm(): FormInterface
    {
        if (!is_null($this->ue)) {
            $this->building?->addUrbanizationEstimate($this->ue);
        }

        return $this->createForm(UrbanizationEstimateType::class, $this->ue);
    }

    /**
     * @throws \Exception
     */
    #[LiveAction]
    public function save(UrbanizationEstimateRepository $urbanizationEstimateRepository): ?Response
    {
        $successMsg = (is_null($this->ue?->getId())) ? 'Se ha agregado el estimado de urbanización.' : 'Se ha modificado el estimado de urbanización.'; // TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var UrbanizationEstimate $ue */
            $ue = $this->getForm()->getData();
            $this->building?->addUrbanizationEstimate($ue);

            $urbanizationEstimateRepository->save($ue, true);

            $this->ue = new UrbanizationEstimate();
            $this->entity = $this->ue;
            if (!is_null($this->modal)) {
                $this->modalManage($ue, 'Se ha sumado el precio del nuevo estimado de urbanización.', [
                    'urbanizationEstimateTotalPrice' => $ue->getBuilding()?->getUrbanizationEstimateTotalPrice(),
                ]);

                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($ue, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('app_urbanization_estimate_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): string
    {
        return 'norender|*';
    }
}
