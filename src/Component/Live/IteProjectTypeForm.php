<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\IteProjectType;
use App\Entity\IteSource;
use App\Form\IteProjectTypeType;
use App\Repository\IteProjectTypeRepository;
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
final class IteProjectTypeForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?IteProjectType $itePT = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp]
    public IteProjectType $entity;

    public function mount(?IteProjectType $itePT = null): void
    {
        $this->itePT = (is_null($itePT)) ? new IteProjectType() : $itePT;
        $this->entity = $this->itePT;
    }

    /**
     * @return FormInterface<IteSource>
     */
    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(IteProjectTypeType::class, $this->itePT);
    }

    #[LiveAction]
    public function save(IteProjectTypeRepository $iteProjectTypeRepository): ?Response
    {
        $successMsg = (is_null($this->itePT?->getId())) ? 'Se ha agregado el tipo de proyecto de ITE.' : 'Se ha modificado el tipo de proyecto de ITE.'; // TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var IteProjectType $itePT */
            $itePT = $this->getForm()->getData();

            $iteProjectTypeRepository->save($itePT, true);

            $this->itePT = new IteProjectType();
            $this->entity = $this->itePT;
            if (!is_null($this->modal)) {
                $this->modalManage($itePT, 'Se ha seleccionado el nuevo tipo de proyecto de ITE.', [
                    'iteProjectType' => $itePT->getId(),
                ]);

                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($itePT, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('app_ite_project_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    /** @SuppressWarnings(PHPMD.UnusedPrivateMethod) */
    private function getDataModelValue(): string
    {
        return 'norender|*';
    }
}
