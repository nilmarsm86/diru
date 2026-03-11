<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\ConstructiveAction;
use App\Form\ConstructiveActionType;
use App\Repository\ConstructiveActionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'component/live/constructive_action_form.html.twig')]
final class ConstructiveActionForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?ConstructiveAction $ca = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp]
    public ConstructiveAction $entity;

    public function mount(?ConstructiveAction $ca = null): void
    {
        $this->ca = (is_null($ca)) ? new ConstructiveAction() : $ca;
        $this->entity = $this->ca;
    }

    /**
     * @return FormInterface<ConstructiveAction>
     */
    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ConstructiveActionType::class, $this->ca);
    }

    #[LiveAction]
    public function save(ConstructiveActionRepository $constructiveActionRepository): ?Response
    {
        $successMsg = (is_null($this->ca?->getId())) ? 'Se ha agregado la acción constructiva.' : 'Se ha modificado la acción constructiva.'; // TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var ConstructiveAction $ca */
            $ca = $this->getForm()->getData();

            $constructiveActionRepository->save($ca, true);

            $this->ca = new ConstructiveAction();
            $this->entity = $this->ca;
            if (!is_null($this->modal)) {
                $this->modalManage($ca, 'Se ha seleccionado la nueva acción constructiva.', [
                    'constructiveAction' => $ca->getId(),
                ]);

                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($ca, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('app_constructive_action_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): string
    {
        return 'norender|*';
    }
}
