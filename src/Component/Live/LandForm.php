<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Building;
use App\Entity\Floor;
use App\Entity\Land;
use App\Form\LandType;
use App\Repository\BuildingRepository;
use App\Repository\FloorRepository;
use App\Repository\LandRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent(template: 'component/live/land_form.html.twig')]
final class LandForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentToolsTrait;
    use ComponentForm;
    use LiveCollectionTrait;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?Land $l = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp]
    public ?Building $building = null;

    #[LiveProp]
    public ?string $route = null;

    public function mount(?Land $land = null, Building $building = null): void
    {
        $this->l = (is_null($land)) ? new Land() : $land;
        $this->building = $building;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(LandType::class, $this->l, [
            'building' => $this->building
        ]);
    }

    /**
     * @throws Exception
     */
    #[LiveAction]
    public function save(Request $request, LandRepository $landRepository, FloorRepository $floorRepository): ?Response
    {
        $successMsg = (is_null($this->l->getId())) ? 'Se han agregado los datos del terreno.' : 'Se han modificado los datos del terreno.';//TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {

            /** @var Land $land */
            $land = $this->getForm()->getData();

            $this->building->setLand($land);
            $showFloorMessage = false;
            //cuando se salva los datos del terreno se crean automaticamente la cantidad de plantas
            if (is_null($land->getId())) {
                $showFloorMessage = true;
                if (empty($this->formValues['floor'])) {
                    $land->setFloor(1);
                    $this->building->setIsNew(true);
                }else{
                    $this->building->setIsNew(false);
                }

                $this->building->createFloors();
            }

            $landRepository->save($land, true);

            $this->l = new Land();
            if (!is_null($this->modal)) {
                $this->modalManage($land, 'Se han salvado los datos del terreno.', [
                    'land' => $land->getId()
                ], 'text-bg-success');

//                if ($land->hasFloors()) {
                    $this->addFlash('success', 'Se han salvado los datos del terreno.');
                    if($showFloorMessage){
                        $this->addFlash('info', 'Se han creado las plantas del inmueble.');
                    }
                    return $this->redirectToRoute('app_floor_index', ['building' => $this->building->getId()], Response::HTTP_SEE_OTHER);
//                } else {
//                    return null;
//                }
            }

            if ($this->ajax) {
                $this->ajaxManage($land, $successMsg);
                return null;
            }

            $this->addFlash('success', $successMsg);
            return $this->redirectToRoute('app_land_edit', ['id' => $land->getId()], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): ?string
    {
        return 'norender|*';
    }

    public function isNew(): bool
    {
        if (!is_null($this->l)) {
            return is_null($this->l->getId());
        }

        return true;
    }

}
