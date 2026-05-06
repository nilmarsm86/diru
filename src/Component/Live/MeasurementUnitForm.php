<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\MeasurementUnit;
use App\Form\MeasurementUnitType;
use App\Repository\MeasurementUnitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent(template: 'component/live/name_and_code_form.html.twig')]
final class MeasurementUnitForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentToolsTrait;
    use ComponentForm;
    use LiveCollectionTrait;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?MeasurementUnit $mu = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp]
    public MeasurementUnit $entity;

    public function mount(?MeasurementUnit $mu = null): void
    {
        $this->mu = (is_null($mu)) ? new MeasurementUnit() : $mu;
        $this->entity = $this->mu;
    }

    /**
     * @return FormInterface<MeasurementUnit>
     */
    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(MeasurementUnitType::class, $this->mu);
    }

    #[LiveAction]
    public function save(MeasurementUnitRepository $measurementUnitRepository): ?Response
    {
        $successMsg = (is_null($this->mu?->getId())) ? 'Se ha agregado la unidad de medida.' : 'Se ha modificado la unidad de medida.'; // TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var MeasurementUnit $measurementUnit */
            $measurementUnit = $this->getForm()->getData();

            $measurementUnitRepository->save($measurementUnit, true);

            $this->mu = new MeasurementUnit();
            $this->entity = $this->mu;
            if (!is_null($this->modal)) {
                $this->modalManage($measurementUnit, 'Se ha seleccionado la nueva unidad de medida.', [
                    'measurement_unit' => $measurementUnit->getId(),
                ]);

                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($measurementUnit, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('app_measurement_unit_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    /** @SuppressWarnings(PHPMD.UnusedPrivateMethod) */
    private function getDataModelValue(): string
    {
        return 'norender|*';
    }
}
