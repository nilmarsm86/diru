<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Building;
use App\Entity\Floor;
use App\Entity\Local;
use App\Entity\Organism;
use App\Entity\SubSystem;
use App\Form\FloorType;
use App\Form\LocalType;
use App\Repository\FloorRepository;
use App\Repository\LocalRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'component/live/local_form.html.twig')]
final class LocalForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?Local $l = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp]
    public ?SubSystem $subSystem = null;

    public function mount(?Local $l = null, SubSystem $subSystem = null): void
    {
        $this->l = (is_null($l)) ? new Local() : $l;
        $this->subSystem = $subSystem;
    }

    protected function instantiateForm(): FormInterface
    {
//        $this->subSystem->addLocal($this->l);
        $this->l->setSubSystem($this->subSystem);
        return $this->createForm(LocalType::class, $this->l, [
            'subSystem' => $this->subSystem
        ]);
    }

    /**
     * @throws Exception
     */
    #[LiveAction]
    public function save(LocalRepository $localRepository): ?Response
    {
        dump($this->formValues);

        $successMsg = (is_null($this->l->getId())) ? 'Se ha agregado el local.' : 'Se ha modificado el local.';//TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var Local $local */
            $local = $this->getForm()->getData();

            $local->setSubSystem($this->subSystem);
            $localRepository->save($local, true);

            $this->l = new Local();
            if (!is_null($this->modal)) {
                $this->modalManage($local, 'Se ha seleccionado el nuevo local agregado.', [
                    'local' => $local->getId()
                ]);
                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($local, $successMsg);
                return null;
            }

            $this->addFlash('success', $successMsg);
            return $this->redirectToRoute('app_local_index', ['subSystem' => $this->subSystem->getId()], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    public function isNew(): bool
    {
        if (!is_null($this->l)) {
            return is_null($this->l->getId());
        }

        return true;
    }

    private function getDataModelValue(): ?string
    {
        return 'norender|*';
    }

}
