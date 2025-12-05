<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Building;
use App\Entity\Floor;
use App\Entity\Organism;
use App\Entity\SubSystem;
use App\Entity\SubsystemTypeSubsystemSubType;
use App\Form\FloorType;
use App\Form\SubSystemType;
use App\Repository\FloorRepository;
use App\Repository\MunicipalityRepository;
use App\Repository\ProvinceRepository;
use App\Repository\SubSystemRepository;
use App\Repository\SubsystemSubTypeRepository;
use App\Repository\SubsystemTypeRepository;
use App\Repository\SubsystemTypeSubsystemSubTypeRepository;
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

#[AsLiveComponent(template: 'component/live/sub_system_form.html.twig')]
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

    #[LiveProp(writable: true)]
    public int $type = 0;

    #[LiveProp(writable: true)]
    public int $subType = 0;

    public function __construct(
        protected readonly SubsystemTypeRepository                 $subsystemTypeRepository,
        protected readonly SubsystemSubTypeRepository              $subsystemSubTypeRepository,
        protected readonly SubsystemTypeSubsystemSubTypeRepository $subsystemTypeSubsystemSubTypeRepository,
    )
    {

    }

    public function mount(?SubSystem $ss = null, Floor $floor = null, bool $reply = false): void
    {
        $this->ss = (is_null($ss)) ? new SubSystem() : $ss;
        $this->entity = $this->ss;
        $this->floor = $floor;
        $this->floor->addSubSystem($this->ss);
        $this->ss->setFloor($this->floor);
        $this->reply = $reply;
    }

    /**
     * @return void
     */
    public function preValue(): void
    {
        if ($this->type !== 0) {
            $this->formValues['subsystemClassification']['type'] = (string)$this->type;
            $this->type = 0;
        }

        if ($this->subType !== 0) {
            $this->formValues['subsystemClassification']['subType'] = (string)$this->subType;
            $this->subType = 0;
        } else {
            if (isset($this->formValues['subsystemClassification'])) {
                if (isset($this->formValues['subsystemClassification']['type'])) {
                    if ($this->formValues['subsystemClassification']['subType']) {
//                        $subType = $this->subsystemSubTypeRepository->find((int)$this->formValues['subsystemClassification']['subType']);
                        $subsystemTypeSubsystemSubType = $this->subsystemTypeSubsystemSubTypeRepository->findOneBy([
                            'subsystemType' => (int)$this->formValues['subsystemClassification']['type'],
                            'subsystemSubType' => (int)$this->formValues['subsystemClassification']['subType'],
                        ]);


                        if (!is_null($subsystemTypeSubsystemSubType)) {
//                            $subType = $subsystemTypeSubsystemSubType->getSubsystemSubType();
//                            $type = $subsystemTypeSubsystemSubType->getSubsystemType();
//                            if ((string)$type->getId() !== $this->formValues['subsystemClassification']['type']) {
//                                $type = $this->subsystemTypeRepository->find((int)$this->formValues['subsystemClassification']['type']);
//                                if (!is_null($type)) {
//                                    $this->formValues['subsystemClassification']['subType'] = ($type->getMunicipalities()->count())
//                                        ? (string)$prov->getMunicipalities()->first()->getId()
//                                        : '';
//                                }
//                            }
                            $this->formValues['subsystemClassification']['subType'] = (string)$subsystemTypeSubsystemSubType->getSubsystemSubType()->getId();
                        }
                    } else {
//                        $prov = $this->provinceRepository->find((int)$this->formValues['address']['province']);
                        $subsystemTypeSubsystemSubType = $this->subsystemTypeSubsystemSubTypeRepository->findOneBy([
                            'subsystemType' => (int)$this->formValues['subsystemClassification']['type'],
//                            'subsystemSubType' => (int)$this->formValues['subsystemClassification']['subType'],
                        ]);
                        if (!is_null($subsystemTypeSubsystemSubType)) {
//                            if ($prov->getMunicipalities()->count()) {
                            $this->formValues['subsystemClassification']['subType'] = (string)$subsystemTypeSubsystemSubType->getSubsystemSubType()->getId();
//                            }
                        }
                    }
                }
            }
        }
    }

    protected function instantiateForm(): FormInterface
    {
        $this->preValue();

        if (!$this->ss->getId()) {
            if (isset($this->formValues['subsystemClassification'])) {
                $type = (int)$this->formValues['subsystemClassification']['type'];
                $subType = (int)$this->formValues['subsystemClassification']['subType'];
            }
        } else {
            $type = (empty($this->formValues['subsystemClassification']['type']) ? $this->ss->getSubsystemTypeSubsystemSubType()->getSubsystemType()->getId() : (int)$this->formValues['subsystemClassification']['type']);
            $subType = (empty($this->formValues['subsystemClassification']['subType']) ? $this->ss->getSubsystemTypeSubsystemSubType()->getSubsystemSubType()->getId() : (int)$this->formValues['subsystemClassification']['subType']);
        }

        $this->floor->addSubSystem($this->ss);
        return $this->createForm(SubSystemType::class, $this->ss, [
            'type' => $type ?? 0,
            'subType' => $subType ?? 0,
            'live_form' => ($this->getDataModelValue() === 'on(change)|*'),
            'modal' => $this->modal
        ]);
    }

    /**
     * @throws Exception
     */
    #[LiveAction]
    public function save(SubSystemRepository $subSystemRepository, EntityManagerInterface $entityManager,): ?Response
    {
        $this->preValue();

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

            //tomar los datos del tipo y subtipo y buscar esa combinacion
            $type = $this->formValues['subsystemClassification']['type'];
            $subType = $this->formValues['subsystemClassification']['subType'];
            $subsystemTypeSubsystemSubType = $entityManager->getRepository(SubsystemTypeSubsystemSubType::class)->findOneBy([
                'subsystemType' => $type,
                'subsystemSubType' => $subType,
            ]);
            if (is_null($subsystemTypeSubsystemSubType)) {
                //si la combinacion no existe, crearla nueva
                $subsystemTypeSubsystemSubType = new SubsystemTypeSubsystemSubType();
                $subsystemTypeSubsystemSubType->setSubsystemType($type);
                $subsystemTypeSubsystemSubType->setSubsystemSubType($subType);

                $entityManager->persist($subsystemTypeSubsystemSubType);
            }

            //asignarle la combinacion al subsistema
            $subSystem->setSubsystemTypeSubsystemSubType($subsystemTypeSubsystemSubType);

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

//    private function getDataModelValue(): ?string
//    {
//        return 'norender|*';
//    }

}
