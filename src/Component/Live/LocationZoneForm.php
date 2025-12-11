<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\LocationZone;
use App\Form\LocationZoneType;
use App\Repository\LocationZoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'partials/live_component/_only_name_form.html.twig')]
final class LocationZoneForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?LocationZone $lz = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp]
    public LocationZone $entity;

    public function mount(?LocationZone $lz = null): void
    {
        $this->lz = (is_null($lz)) ? new LocationZone() : $lz;
        $this->entity = $this->lz;
    }

    /**
     * @return FormInterface<LocationZone>
     */
    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(LocationZoneType::class, $this->lz);
    }

    /**
     * @throws \Exception
     */
    #[LiveAction]
    public function save(LocationZoneRepository $locationZoneRepository): ?Response
    {
        $successMsg = (is_null($this->lz?->getId())) ? 'Se ha agregado la zona de ubicación.' : 'Se ha modificado la zona de ubicación.'; // TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var LocationZone $lz */
            $lz = $this->getForm()->getData();

            $locationZoneRepository->save($lz, true);

            $this->lz = new LocationZone();
            $this->entity = $this->lz;
            if (!is_null($this->modal)) {
                $this->modalManage($lz, 'Se ha seleccionado la nueva zona de ubicación.', [
                    'locationZone' => $lz->getId(),
                ]);

                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($lz, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('app_location_zone_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): string
    {
        return 'norender|*';
    }
}
