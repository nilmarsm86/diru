<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Component\Twig\Modal\Modal;
use App\Entity\GeographicLocation;
use App\Entity\LocationZone;
use App\Entity\Organism;
use App\Form\GeographicLocationType;
use App\Form\LocationZoneType;
use App\Form\OrganismType;
use App\Repository\GeographicLocationRepository;
use App\Repository\LocationZoneRepository;
use App\Repository\OrganismRepository;
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

#[AsLiveComponent(template: 'component/live/geographic_location_form.html.twig')]
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

    public function mount(?GeographicLocation $gl = null): void
    {
        $this->gl = (is_null($gl)) ? new GeographicLocation() : $gl;
    }

//    /**
//     * @param GeographicLocation $gl
//     * @param string $message
//     * @return void
//     */
//    private function modalManage(GeographicLocation $gl, string $message=''): void
//    {
//        $template = $this->getSuccessTemplate($gl, empty($message) ? 'Seleccione la nueva ubicación geográfica agregada.' : $message);
//
//        $this->dispatchBrowserEvent('type--entity-plus:update', [
//            'data' => [
//                'geographic_location' => $gl->getId()
//            ],
//            'modal' => $this->modal,
//            'response' => $template
//        ]);
//
//        $this->dispatchBrowserEvent(Modal::MODAL_CLOSE);
//
//        $this->gl = new GeographicLocation();
//        $this->resetForm();//establecer un objeto nuevo
//    }
//
//    /**
//     * @param string $successMsg
//     * @return void
//     */
//    private function ajaxManage(string $successMsg): void
//    {
//        $template = $this->renderView("partials/_form_success.html.twig", [
//            'id' => 'new_' . $this->getClassName($this->lz::class) . '_' . $this->lz->getId(),
//            'type' => 'text-bg-success',
//            'message' => $successMsg
//        ]);
//
//        $this->lz = new LocationZone();
//        $this->emitSuccess([
//            'response' => $template
//        ]);
//    }

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
        $successMsg = (is_null($this->gl->getId())) ? 'Se ha agregado la ubicación geográfica.' : 'Se ha modificado la ubicación geográfica.';//TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var GeographicLocation $gl */
            $gl = $this->getForm()->getData();

            $geographicLocationRepository->save($gl, true);

            if (!is_null($this->modal)) {
//                $this->modalManage($lz);
                $this->modalManage($gl, 'Seleccione la nueva ubicación geográfica agregada.', [
                    'geographic_location' => $gl->getId()
                ]);
                return null;
            }

            if ($this->ajax) {
//                $this->ajaxManage($successMsg);
                $this->ajaxManage($gl, $successMsg);
                return null;
            }

            $this->addFlash('success', $successMsg);
            return $this->redirectToRoute('app_geographic_location_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): ?string
    {
        return 'norender|*';
    }

}
