<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\UrbanRegulation;
use App\Entity\UrbanRegulationType;
use App\Repository\UrbanRegulationRepository;
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

#[AsLiveComponent(template: 'component/live/urban_regulation_form.html.twig')]
final class UrbanRegulationForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?UrbanRegulation $ur = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp]
    public UrbanRegulation $entity;

    public function mount(?UrbanRegulation $ur = null): void
    {
        $this->ur = (is_null($ur)) ? new UrbanRegulation() : $ur;
        $this->entity = $this->ur;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(\App\Form\UrbanRegulationType::class, $this->ur);
    }

    /**
     * @throws Exception
     */
    #[LiveAction]
    public function save(UrbanRegulationRepository $urbanRegulationRepository): ?Response
    {
        $successMsg = (is_null($this->ur->getId())) ? 'Se ha agregado la regulación urbana.' : 'Se ha modificado la regulación urbana.';//TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var UrbanRegulation $ur */
            $ur = $this->getForm()->getData();

            $urbanRegulationRepository->save($ur, true);

            $this->ur = new UrbanRegulation();
            $this->entity = $this->ur;
            if (!is_null($this->modal)) {
                $this->modalManage($ur, 'Se ha seleccionado la regulación urbana.', [
                    'urbanRegulation' => $ur->getId()
                ]);
                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($ur, $successMsg);
                return null;
            }

            $this->addFlash('success', $successMsg);
            return $this->redirectToRoute('app_urban_regulation_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): ?string
    {
        return 'norender|*';
    }

}
