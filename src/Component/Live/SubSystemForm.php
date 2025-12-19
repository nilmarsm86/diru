<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Floor;
use App\Entity\SubSystem;
use App\Entity\SubsystemTypeSubsystemSubType;
use App\Form\SubSystemType;
use App\Repository\SubSystemRepository;
use App\Repository\SubsystemSubTypeRepository;
use App\Repository\SubsystemTypeRepository;
use App\Repository\SubsystemTypeSubsystemSubTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        protected readonly SubsystemTypeRepository $subsystemTypeRepository,
        protected readonly SubsystemSubTypeRepository $subsystemSubTypeRepository,
        protected readonly SubsystemTypeSubsystemSubTypeRepository $subsystemTypeSubsystemSubTypeRepository,
    ) {
    }

    public function mount(?SubSystem $ss = null, ?Floor $floor = null, bool $reply = false): void
    {
        $this->ss = (is_null($ss)) ? new SubSystem() : $ss;
        $this->entity = $this->ss;
        $this->floor = $floor;
        $this->floor?->addSubSystem($this->ss);
        $this->ss->setFloor($this->floor);
        $this->reply = $reply;
    }

    public function preValue(): void
    {
        if (0 !== $this->type) {
            //            $this->formValues['subsystemClassification']['type'] = (string) $this->type;
            /** @var array<string, array<int, mixed>> $formValues */
            $formValues = $this->formValues;
            $formValues['subsystemClassification']['type'] = (string) $this->type;
            $this->formValues = $formValues;
            $this->type = 0;
        }

        if (0 !== $this->subType) {
            /** @var array<string, array<int, mixed>> $formValues */
            $formValues = $this->formValues;
            $formValues['subsystemClassification']['subType'] = (string) $this->subType;
            $this->formValues = $formValues;
            $this->subType = 0;
        } else {
            if (isset($this->formValues['subsystemClassification'])) {
                /** @var array<string, array<int, mixed>> $formValues */
                $formValues = $this->formValues;
                if (isset($formValues['subsystemClassification']['type'])) {
                    if ($formValues['subsystemClassification']['subType']) {
                        //                        $subType = $this->subsystemSubTypeRepository->find((int)$this->formValues['subsystemClassification']['subType']);
                        $this->formValues = $formValues;
                        /** @var int $type */
                        $type = $this->formValues['subsystemClassification']['type'];
                        /** @var int $subType */
                        $subType = $this->formValues['subsystemClassification']['subType'];
                        $subsystemTypeSubsystemSubType = $this->subsystemTypeSubsystemSubTypeRepository->findOneBy([
                            'subsystemType' => $type,
                            'subsystemSubType' => $subType,
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
                            $formValues['subsystemClassification']['subType'] = (string) $subsystemTypeSubsystemSubType->getSubsystemSubType()?->getId();
                            $this->formValues = $formValues;
                        }
                    } else {
                        //                        $prov = $this->provinceRepository->find((int)$this->formValues['address']['province']);
                        /** @var int $type */
                        $type = $formValues['subsystemClassification']['type'];
                        $subsystemTypeSubsystemSubType = $this->subsystemTypeSubsystemSubTypeRepository->findOneBy([
                            'subsystemType' => $type,
                            //                            'subsystemSubType' => (int)$this->formValues['subsystemClassification']['subType'],
                        ]);
                        if (!is_null($subsystemTypeSubsystemSubType)) {
                            //                            if ($prov->getMunicipalities()->count()) {
                            $formValues['subsystemClassification']['subType'] = (string) $subsystemTypeSubsystemSubType->getSubsystemSubType()?->getId();
                            $this->formValues = $formValues;
                            //                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @return FormInterface<SubSystem>
     */
    protected function instantiateForm(): FormInterface
    {
        $this->preValue();

        /** @var array<string, array<string, mixed>> $formValues */
        $formValues = $this->formValues;

        $type = 0;
        $subType = 0;
        if (null === $this->ss?->getId()) {
            if (isset($formValues['subsystemClassification'])) {
                /** @var int $type */
                $type = $formValues['subsystemClassification']['type'];
                /** @var int $subType */
                $subType = $formValues['subsystemClassification']['subType'];
            }
        } else {
            $subsystemTypeSubsystemSubType = $this->ss->getSubsystemTypeSubsystemSubType();
            /** @var int $type */
            $type = $formValues['subsystemClassification']['type'] ?? $subsystemTypeSubsystemSubType?->getSubsystemType()?->getId();
            /** @var int $subType */
            $subType = $formValues['subsystemClassification']['subType'] ?? $subsystemTypeSubsystemSubType?->getSubsystemSubType()?->getId();
        }

        assert($this->ss instanceof SubSystem);
        $this->floor?->addSubSystem($this->ss);

        return $this->createForm(SubSystemType::class, $this->ss, [
            'type' => (int) $type,
            'subType' => (int) $subType,
            'live_form' => ('on(change)|*' === $this->getDataModelValue()),
            'modal' => $this->modal,
        ]);
    }

    #[LiveAction]
    public function save(SubSystemRepository $subSystemRepository, EntityManagerInterface $entityManager): ?Response
    {
        $this->preValue();

        $successMsg = (is_null($this->ss?->getId())) ? 'Se ha agregado el subsistema.' : 'Se ha modificado el subsistema.'; // TODO: personalizar los mensajes

        //        if(is_null($this->ss->getId())){
        //            $subSystem = $subSystemRepository->findBy([
        //                'name' => $this->formValues['name'],
        //                'floor' => $this->floor
        //            ]);
        //
        //            if(count($subSystem) > 0){
        // //                dump("Error");
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
            if (is_null($this->ss?->getId())) {
                assert($this->floor instanceof Floor);
                /** @var string $name */
                $name = $this->formValues['name'];
                $subSystem = SubSystem::createAutomatic($subSystem, $this->floor, $name, $this->reply, $entityManager);
            }

            /** @var array<string, array<string, mixed>> $formValues */
            $formValues = $this->formValues;
            // tomar los datos del tipo y subtipo y buscar esa combinacion
            $type = $formValues['subsystemClassification']['type'];
            $subType = $formValues['subsystemClassification']['subType'];
            $subsystemTypeSubsystemSubType = $entityManager->getRepository(SubsystemTypeSubsystemSubType::class)->findOneBy([
                'subsystemType' => $type,
                'subsystemSubType' => $subType,
            ]);
            if (is_null($subsystemTypeSubsystemSubType)) {
                $type = $entityManager->getRepository(\App\Entity\SubsystemType::class)->find($type);
                $subType = $entityManager->getRepository(\App\Entity\SubsystemSubType::class)->find($subType);

                // si la combinacion no existe, crearla nueva
                $subsystemTypeSubsystemSubType = new SubsystemTypeSubsystemSubType();
                if (!is_null($type)) {
                    $subsystemTypeSubsystemSubType->setSubsystemType($type);
                }

                if (!is_null($subType)) {
                    $subsystemTypeSubsystemSubType->setSubsystemSubType($subType);
                }

                $entityManager->persist($subsystemTypeSubsystemSubType);
            }

            // asignarle la combinacion al subsistema
            $subSystem->setSubsystemTypeSubsystemSubType($subsystemTypeSubsystemSubType);

            $subSystemRepository->save($subSystem, true);

            $this->ss = new SubSystem();

            $this->entity = $this->ss;
            if (!is_null($this->modal)) {
                $this->modalManage($subSystem, 'Se ha seleccionado el nuevo sub sistema agregado.', [
                    'subSystem' => $subSystem->getId(),
                ]);

                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($subSystem, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('app_sub_system_index', ['floor' => $this->floor?->getId()], Response::HTTP_SEE_OTHER);
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
