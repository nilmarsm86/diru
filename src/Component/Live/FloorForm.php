<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Building;
use App\Entity\Floor;
use App\Entity\Organism;
use App\Form\FloorType;
use App\Repository\FloorRepository;
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

#[AsLiveComponent(template: 'component/live/floor_form.html.twig')]
final class FloorForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?Floor $fl = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp]
    public Floor $entity;

    #[LiveProp]
    public ?Building $building = null;

    public function mount(?Floor $fl = null, Building $building = null): void
    {
        $this->fl = (is_null($fl)) ? new Floor() : $fl;
        $this->entity = $this->fl;
        $this->building = $building;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(FloorType::class, $this->fl);
    }

    /**
     * @throws Exception
     */
    #[LiveAction]
    public function save(FloorRepository $floorRepository): ?Response
    {
        $successMsg = (is_null($this->fl->getId())) ? 'Se ha agregado la planta.' : 'Se ha modificado la planta.';//TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var Floor $floor */
            $floor = $this->getForm()->getData();

            $floorRepository->save($floor, true);

            $this->fl = new Floor();
            $this->entity = $this->fl;
            if (!is_null($this->modal)) {
                $this->modalManage($floor, 'Se ha seleccionado la nueva planta agregada.', [
                    'floor' => $floor->getId()
                ]);
                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($floor, $successMsg);
                return null;
            }

            $this->addFlash('success', $successMsg);
            return $this->redirectToRoute('app_floor_index', ['building' => $this->building->getId()], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    public function isNew(): bool
    {
        if (!is_null($this->fl)) {
            return is_null($this->fl->getId());
        }

        return true;
    }

}
