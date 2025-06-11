<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Building;
use App\Entity\Land;
use App\Form\LandType;
use App\Repository\LandRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
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

    public function mount(?Land $land = null, Building $building = null): void
    {
        $this->l = (is_null($land)) ? new Land() : $land;
        $this->building = $building;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(LandType::class, $this->l);
    }

    /**
     * @throws Exception
     */
    #[LiveAction]
    public function save(LandRepository $landRepository): ?Response
    {
        $successMsg = (is_null($this->l->getId())) ? 'Se han agregado los datos del terreno.' : 'Se han modificado los datos del terreno.';//TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var Land $land */
            $land = $this->getForm()->getData();

            $this->building->setLand($land);

            $landRepository->save($land, true);

            $this->l = new Land();
            if (!is_null($this->modal)) {
                $this->modalManage($land, 'Se han salvado los datos del terreno', [
                    'land' => $land->getId()
                ], 'text-bg-success');
                return null;
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
        if(!is_null($this->l)){
            return is_null($this->l->getId());
        }

        return true;
    }

}
