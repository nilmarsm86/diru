<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Ite;
use App\Form\IteType;
use App\Repository\IteRepository;
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

#[AsLiveComponent(template: 'component/live/ite_form.html.twig')]
final class IteForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentToolsTrait;
    use ComponentForm;
    use LiveCollectionTrait;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?Ite $indicator = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp]
    public Ite $entity;

    #[LiveProp]
    public \App\Entity\Enums\IteType $type;

    #[LiveProp(writable: true)]
    public ?string $measurementUnit = null;

    public function mount(?Ite $ite = null): void
    {
        $this->indicator = $ite;

        if (is_null($this->indicator)) {
            $this->indicator = new Ite();
        } else {
            if (!is_null($this->indicator->getMeasurementUnit())) {
                $this->measurementUnit = (string) $this->indicator->getMeasurementUnit()->getId();
            }
        }
        $this->entity = $this->indicator;
    }

    /**
     * @return FormInterface<Ite>
     */
    protected function instantiateForm(): FormInterface
    {
        if (!is_null($this->measurementUnit)) {
            $this->formValues['measurementUnit'] = $this->measurementUnit;
        }

        return $this->createForm(IteType::class, $this->indicator);
    }

    #[LiveAction]
    public function save(IteRepository $iteRepository, MeasurementUnitRepository $measurementUnitRepository): ?Response
    {
        $successMsg = (is_null($this->indicator?->getId())) ? 'Se ha agregado el ITE.' : 'Se ha modificado el ITE.'; // TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var Ite $ite */
            $ite = $this->getForm()->getData();

            $ite->setType($this->type);

            $measurementUnit = $measurementUnitRepository->find($this->measurementUnit);
            $ite->setMeasurementUnit($measurementUnit);

            $iteRepository->save($ite, true);

            $this->indicator = new Ite();
            $this->entity = $this->indicator;
            if (!is_null($this->modal)) {
                $this->modalManage($ite, 'Se ha seleccionado el ITE agregado.', [
                    'ite' => $ite->getId(),
                ]);

                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($ite, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('ite_upload_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    /** @SuppressWarnings(PHPMD.UnusedPrivateMethod) */
    private function getDataModelValue(): string
    {
        return 'norender|*';
    }
}
