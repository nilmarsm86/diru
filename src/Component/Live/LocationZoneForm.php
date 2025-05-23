<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Component\Twig\Modal\Modal;
use App\Entity\LocationZone;
use App\Entity\Organism;
use App\Form\LocationZoneType;
use App\Form\OrganismType;
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

#[AsLiveComponent(template: 'component/live/location_zone_form.html.twig')]
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

    public function mount(?LocationZone $lz = null): void
    {
        $this->lz = (is_null($lz)) ? new LocationZone() : $lz;
    }

//    /**
//     * @param LocationZone $lz
//     * @param string $message
//     * @return void
//     */
//    private function modalManage(LocationZone $lz, string $message=''): void
//    {
//        $template = $this->getSuccessTemplate($lz, empty($message) ? 'Seleccione la nueva zona de ubicación.' : $message);
//
//        $this->dispatchBrowserEvent('type--entity-plus:update', [
//            'data' => [
//                'location_zone' => $lz->getId()
//            ],
//            'modal' => $this->modal,
//            'response' => $template
//        ]);
//
//        $this->dispatchBrowserEvent(Modal::MODAL_CLOSE);
//
//        $this->lz = new LocationZone();
//        $this->resetForm();//establecer un objeto provincia nuevo
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
        return $this->createForm(LocationZoneType::class, $this->lz);
    }

    /**
     * @throws Exception
     */
    #[LiveAction]
    public function save(LocationZoneRepository $locationZoneRepository): ?Response
    {
        $successMsg = (is_null($this->lz->getId())) ? 'Se ha agregado la zona de ubicación.' : 'Se ha modificado la zona de ubicación.';//TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var LocationZone $lz */
            $lz = $this->getForm()->getData();

            $locationZoneRepository->save($lz, true);

            $this->lz = new LocationZone();
            if (!is_null($this->modal)) {
//                $this->modalManage($lz);
                $this->modalManage($lz, 'Seleccione la nueva zona de ubicación.', [
                    'locationZone' => $lz->getId()
                ]);
                return null;
            }

            if ($this->ajax) {
//                $this->ajaxManage($successMsg);
                $this->ajaxManage($lz, $successMsg);
                return null;
            }

            $this->addFlash('success', $successMsg);
            return $this->redirectToRoute('app_location_zone_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

}
