<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Building;
use App\Entity\JustValueEstimate;
use App\Form\JustValueEstimateType;
use App\Repository\JustValueEstimateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'component/live/just_value_estimate_form.html.twig')]
final class JustValueEstimateForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?JustValueEstimate $ue = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp]
    public JustValueEstimate $entity;

    #[LiveProp]
    public ?Building $building = null;

    public function mount(?JustValueEstimate $ue = null, ?Building $building = null): void
    {
        $this->ue = (is_null($ue)) ? new JustValueEstimate() : $ue;
        $this->entity = $this->ue;
        $this->building = $building;
        $this->building?->addJustValueEstimate($this->ue);
    }

    /**
     * @return FormInterface<JustValueEstimate>
     */
    protected function instantiateForm(): FormInterface
    {
        if (!is_null($this->ue)) {
            $this->building?->addJustValueEstimate($this->ue);
        }

        return $this->createForm(JustValueEstimateType::class, $this->ue);
    }

    #[LiveAction]
    public function save(JustValueEstimateRepository $justValueEstimateRepository): ?Response
    {
        $successMsg = (is_null($this->ue?->getId())) ? 'Se ha agregado el valor ajustado.' : 'Se ha modificado el valor ajustado.'; // TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var JustValueEstimate $ue */
            $ue = $this->getForm()->getData();
            $this->building?->addJustValueEstimate($ue);

            $justValueEstimateRepository->save($ue, true);

            $this->ue = new JustValueEstimate();
            $this->entity = $this->ue;
            if (!is_null($this->modal)) {
                $this->modalManage($ue, 'Valor ajustado agregado.', [
                    'justValueEstimateTotalPrice' => $ue->getBuilding()?->getJustValueEstimateTotalPrice(),
                ]);

                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($ue, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('app_just_value_estimate_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): string
    {
        return 'norender|*';
    }
}
