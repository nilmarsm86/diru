<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\LocationZone;
use App\Entity\UrbanRegulationType;
use App\Form\LocationZoneType;
use App\Form\UrbanRegulationTypeType;
use App\Repository\LocationZoneRepository;
use App\Repository\UrbanRegulationTypeRepository;
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
final class UrbanRegulationTypeForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?UrbanRegulationType $urt = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp]
    public UrbanRegulationType $entity;

    public function mount(?UrbanRegulationType $urt = null): void
    {
        $this->urt = (is_null($urt)) ? new UrbanRegulationType() : $urt;
        $this->entity = $this->urt;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(UrbanRegulationTypeType::class, $this->urt);
    }

    /**
     * @throws Exception
     */
    #[LiveAction]
    public function save(UrbanRegulationTypeRepository $urbanRegulationTypeRepository): ?Response
    {
        $successMsg = (is_null($this->urt->getId())) ? 'Se ha agregado el tipo de regulación urbana.' : 'Se ha modificado el tipo de regulación urbana.';//TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var UrbanRegulationType $urt */
            $urt = $this->getForm()->getData();

            $urbanRegulationTypeRepository->save($urt, true);

            $this->urt = new UrbanRegulationType();
            $this->entity = $this->urt;
            if (!is_null($this->modal)) {
                $this->modalManage($urt, 'Se ha seleccionado el tipo de regulación urbana.', [
                    'urbanRegulationType' => $urt->getId()
                ]);
                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($urt, $successMsg);
                return null;
            }

            $this->addFlash('success', $successMsg);
            return $this->redirectToRoute('app_urban_regulation_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): ?string
    {
        return 'norender|*';
    }

}
