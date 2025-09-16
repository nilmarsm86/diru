<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Building;
use App\Entity\Floor;
use App\Entity\Organism;
use App\Entity\SubSystem;
use App\Form\FloorType;
use App\Form\SubSystemType;
use App\Repository\FloorRepository;
use App\Repository\SubSystemRepository;
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

#[AsLiveComponent(template: '_only_name_form.html.twig')]
final class SubSystemForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?SubSystem $ss = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp]
    public SubSystem $entity;

    #[LiveProp]
    public ?Floor $floor = null;

    #[LiveProp]
    public bool $reply = false;

    public function mount(?SubSystem $ss = null, Floor $floor = null, bool $reply = false): void
    {
        $this->ss = (is_null($ss)) ? new SubSystem() : $ss;
        $this->entity = $this->ss;
        $this->floor = $floor;
        $this->floor->addSubSystem($this->ss);
        $this->ss->setFloor($this->floor);
        $this->reply = $reply;
    }

    protected function instantiateForm(): FormInterface
    {
        $this->floor->addSubSystem($this->ss);
        return $this->createForm(SubSystemType::class, $this->ss);
    }

    /**
     * @throws Exception
     */
    #[LiveAction]
    public function save(SubSystemRepository $subSystemRepository, EntityManagerInterface $entityManager): ?Response
    {
        $successMsg = (is_null($this->ss->getId())) ? 'Se ha agregado el subsistema.' : 'Se ha modificado el subsistema.';//TODO: personalizar los mensajes



//        if(is_null($this->ss->getId())){
//            $subSystem = $subSystemRepository->findBy([
//                'name' => $this->formValues['name'],
//                'floor' => $this->floor
//            ]);
//
//            if(count($subSystem) > 0){
////                dump("Error");
//                $errorMessage = new FormError('Ya existe una subsistema con este nombre en esta planta.');
//                $this->getForm()->get
//            }
//        }
//
//        //die();

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var SubSystem $subSystem */
            $subSystem = $this->getForm()->getData();
//            $this->floor->addSubSystem($subSystem);
//            $this->ss->setFloor($this->floor);
//            $subSystem->setFloor($this->floor);
//            $subSystem->createInitialLocal($this->reply, $entityManager);

//            if($this->floor->inNewBuilding()){
//                $subSystem->recent();
//            }

//            $local->setOriginal(0);
//            if($this->reply){
//                $subSystem->setHasReply(false);
//                $subSystem->recent();
//            }else{
//                if($this->floor->inNewBuilding()){
//                    $subSystem->recent();
//                }else{
//                    $subSystem->existingWithoutReplicating();
//                }
//            }
            if (is_null($this->ss->getId())) {
                $subSystem = SubSystem::createAutomatic($subSystem, $this->floor, $this->formValues['name'], $this->reply, $entityManager);
            }
            $subSystemRepository->save($subSystem, true);

            $this->ss = new SubSystem();

            $this->entity = $this->ss;
            if (!is_null($this->modal)) {
                $this->modalManage($subSystem, 'Se ha seleccionado el nuevo sub sistema agregado.', [
                    'subSystem' => $subSystem->getId()
                ]);
                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($subSystem, $successMsg);
                return null;
            }

            $this->addFlash('success', $successMsg);
            return $this->redirectToRoute('app_sub_system_index', ['floor' => $this->floor->getId()], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    public function isNew(): bool
    {
        if (!is_null($this->ss)) {
            return is_null($this->ss->getId());
        }

        return true;
    }

}
