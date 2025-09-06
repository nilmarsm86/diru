<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Building;
use App\Entity\ConstructiveAction;
use App\Entity\Enums\ConstructiveActionType;
use App\Entity\Enums\TechnicalStatus;
use App\Entity\Enums\StructureState;
use App\Entity\Floor;
use App\Entity\Local;
use App\Entity\LocalConstructiveAction;
use App\Entity\Organism;
use App\Entity\SubSystem;
use App\Form\FloorType;
use App\Form\LocalType;
use App\Repository\FloorRepository;
use App\Repository\LocalRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    #[LiveProp]
    public ?LocalConstructiveAction $localConstructiveAction = null;

    #[LiveProp]
    public bool $reply = false;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {

    }

    public function mount(?Local $l = null, SubSystem $subSystem = null, bool $reply = false): void
    {
        $this->l = (is_null($l)) ? new Local() : $l;
        $this->subSystem = $subSystem;
        $this->localConstructiveAction = $this->l->getLocalConstructiveAction();
        $this->reply = $reply;
    }

    /**
     * @return void
     */
    public function setDefaultTechnicalStatus(): void
    {
        if (is_null($this->l->getId()) && $this->subSystem->getState() !== StructureState::ExistingWithoutReplicating) {
            $this->l->setTechnicalStatus(TechnicalStatus::Good);
        }
    }

    /**
     * @return void
     */
    public function setDefaultConstructiveAction(): void
    {
        if (is_null($this->l->getLocalConstructiveAction()) && ($this->reply === true || $this->l->inNewBuilding()) && is_null($this->l->getOriginal())) {
            $constructiveAction = $this->entityManager->getRepository(ConstructiveAction::class)->findOneBy(['name' => 'Obra nueva']);

            $localConstructiveAction = new LocalConstructiveAction();
            $localConstructiveAction->setConstructiveAction($constructiveAction);
            $this->l->setLocalConstructiveAction($localConstructiveAction);
        }
    }

    protected function instantiateForm(): FormInterface
    {
//        $this->subSystem->addLocal($this->l);
        $this->l->setSubSystem($this->subSystem);
        $this->setDefaultConstructiveAction();
        $this->setDefaultTechnicalStatus();

        return $this->createForm(LocalType::class, $this->l, [
            'subSystem' => $this->subSystem,
            'reply' => $this->reply
        ]);
    }

    /**
     * @throws Exception
     */
    #[LiveAction]
    public function save(LocalRepository $localRepository): ?Response
    {
        $successMsg = (is_null($this->l->getId())) ? 'Se ha agregado el local.' : (($this->reply) ? 'Se ha modificado el local replicado.' : 'Se ha modificado el local.');//TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var Local $local */
            $local = $this->getForm()->getData();

            if (is_null($this->l->getId())) {
                $local = Local::createAutomaticLocal($local, $this->subSystem, $this->formValues['area'], $this->formValues['number'], $this->reply, $this->entityManager);
            }

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
