<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Building;
use App\Entity\BuildingRevision;
use App\Entity\LocationZone;
use App\Form\BuildingRevisionType;
use App\Form\LocationZoneType;
use App\Repository\BuildingRevisionRepository;
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

#[AsLiveComponent(template: 'component/live/building_revision_form.html.twig')]
final class BuildingRevisionForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?BuildingRevision $br = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp]
    public BuildingRevision $entity;

    #[LiveProp]
    public ?Building $building = null;

    public function mount(?BuildingRevision $br = null, ?Building $building = null): void
    {
        $this->br = (is_null($br)) ? new BuildingRevision() : $br;
        $this->entity = $this->br;
        $this->building = $building;
        $this->building?->addBuildingRevision($this->br);
    }

    /**
     * @return FormInterface<BuildingRevision>
     */
    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(BuildingRevisionType::class, $this->br);
    }

    #[LiveAction]
    public function save(BuildingRevisionRepository $buildingRevisionRepository): ?Response
    {
        $successMsg = (is_null($this->br?->getId())) ? 'Se ha agregado la revisión.' : 'Se ha modificado la revisión.'; // TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var BuildingRevision $br */
            $br = $this->getForm()->getData();
            $this->building?->addBuildingRevision($br);

            $buildingRevisionRepository->save($br, true);

            $this->br = new BuildingRevision();
            $this->entity = $this->br;
            if (!is_null($this->modal)) {
                $this->modalManage($br, 'Se ha agregado la nueva revisión.', [
                    'buildingRevision' => $br->getId(),
                ]);

                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($br, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('app_building_revision_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): string
    {
        return 'norender|*';
    }
}
