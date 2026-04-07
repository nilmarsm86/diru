<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\ConstructiveSystem;
use App\Form\ConstructiveSystemType;
use App\Repository\ConstructiveSystemRepository;
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
final class ConstructiveSystemForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?ConstructiveSystem $lz = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp]
    public ConstructiveSystem $entity;

    public function mount(?ConstructiveSystem $cs = null): void
    {
        $this->lz = (is_null($cs)) ? new ConstructiveSystem() : $cs;
        $this->entity = $this->lz;
    }

    /**
     * @return FormInterface<ConstructiveSystem>
     */
    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ConstructiveSystemType::class, $this->lz);
    }

    #[LiveAction]
    public function save(ConstructiveSystemRepository $locationZoneRepository): ?Response
    {
        $successMsg = (is_null($this->lz?->getId())) ? 'Se ha agregado el sistema constructivo.' : 'Se ha modificado el sistema constructivo.'; // TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var ConstructiveSystem $lz */
            $lz = $this->getForm()->getData();

            $locationZoneRepository->save($lz, true);

            $this->lz = new ConstructiveSystem();
            $this->entity = $this->lz;
            if (!is_null($this->modal)) {
                $this->modalManage($lz, 'Se ha seleccionado el nuevo sistema constructivo.', [
                    'constructiveSystem' => $lz->getId(),
                ]);

                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($lz, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('app_constructive_system_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    /** @SuppressWarnings(PHPMD.UnusedPrivateMethod) */
    private function getDataModelValue(): string
    {
        return 'norender|*';
    }
}
