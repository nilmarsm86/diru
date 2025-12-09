<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\GeographicLocation;
use App\Form\GeographicLocationType;
use App\Repository\GeographicLocationRepository;
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

#[AsLiveComponent(template: 'partials/live_component/_only_name_form.html.twig')]
final class GeographicLocationForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?GeographicLocation $gl = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp]
    public GeographicLocation $entity;

    public function mount(?GeographicLocation $gl = null): void
    {
        $this->gl = (is_null($gl)) ? new GeographicLocation() : $gl;
        $this->entity = $this->gl;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(GeographicLocationType::class, $this->gl);
    }

    /**
     * @throws Exception
     */
    #[LiveAction]
    public function save(GeographicLocationRepository $geographicLocationRepository): ?Response
    {
        $successMsg = (is_null($this->gl?->getId())) ? 'Se ha agregado la ubicación geográfica.' : 'Se ha modificado la ubicación geográfica.';//TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var GeographicLocation $gl */
            $gl = $this->getForm()->getData();

            $geographicLocationRepository->save($gl, true);

            $this->gl = new GeographicLocation();
            $this->entity = $this->gl;
            if (!is_null($this->modal)) {
                $this->modalManage($gl, 'Se ha seleccionado la nueva ubicación geográfica agregada.', [
                    'geographic_location' => $gl->getId()
                ]);
                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($gl, $successMsg);
                return null;
            }

            $this->addFlash('success', $successMsg);
            return $this->redirectToRoute('app_geographic_location_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): string
    {
        return 'norender|*';
    }

}
