<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\IteSource;
use App\Form\IteSourceType;
use App\Repository\IteSourceRepository;
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
final class IteSourceForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?IteSource $iteS = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp]
    public IteSource $entity;

    public function mount(?IteSource $iteS = null): void
    {
        $this->iteS = (is_null($iteS)) ? new IteSource() : $iteS;
        $this->entity = $this->iteS;
    }

    /**
     * @return FormInterface<IteSource>
     */
    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(IteSourceType::class, $this->iteS);
    }

    #[LiveAction]
    public function save(IteSourceRepository $iteSourceRepository): ?Response
    {
        $successMsg = (is_null($this->iteS?->getId())) ? 'Se ha agregado la fuente de información.' : 'Se ha modificado la fuente de información.'; // TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var IteSource $iteS */
            $iteS = $this->getForm()->getData();

            $iteSourceRepository->save($iteS, true);

            $this->iteS = new IteSource();
            $this->entity = $this->iteS;
            if (!is_null($this->modal)) {
                $this->modalManage($iteS, 'Se ha seleccionado la nueva fuente de información.', [
                    'iteSource' => $iteS->getId(),
                ]);

                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($iteS, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('app_ite_source_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    /** @SuppressWarnings(PHPMD.UnusedPrivateMethod) */
    private function getDataModelValue(): string
    {
        return 'norender|*';
    }
}
