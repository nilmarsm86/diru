<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Province;
use App\Entity\SubsystemType;
use App\Form\ProvinceType;
use App\Form\SubsystemTypeType;
use App\Repository\ProvinceRepository;
use App\Repository\SubsystemSubTypeRepository;
use App\Repository\SubsystemTypeRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent(template: 'component/live/subsystem_type_form.html.twig')]
final class SubsystemTypeForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentToolsTrait;
    use ComponentForm;
    use LiveCollectionTrait;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?SubsystemType $sst = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp]
    public SubsystemType $entity;

    #[LiveProp(writable: true)]
    public ?string $subsystemSubType = null;

    #[LiveProp(writable: true)]
    public ?string $subsystemSubTypePosition = null;

    public function mount(?SubsystemType $sst = null): void
    {
        $this->sst = (is_null($sst)) ? new SubsystemType() : $sst;
        $this->entity = $this->sst;
    }

    protected function instantiateForm(): FormInterface
    {
        if(!is_null($this->subsystemSubType)){
            $this->formValues['subsystemSubTypes'][(count($this->formValues['subsystemSubTypes']) - 1)]['name'] = $this->subsystemSubType;
        }

        return $this->createForm(SubsystemTypeType::class, $this->sst, [
//            'screen' => 'building'
        ]);
    }

    /**
     * @throws Exception
     */
    #[LiveAction]
    public function save(SubsystemTypeRepository $subsystemTypeRepository, SubsystemSubTypeRepository $subsystemSubTypeRepository): ?Response
    {
        $successMsg = (is_null($this->sst->getId())) ? 'Se ha agregado el tipo.' : 'Se ha modificado el tipo.';//TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var SubsystemType $subsystemType */
            $subsystemType = $this->getForm()->getData();

            foreach ($subsystemType->getSubsystemSubTypes() as $subsystemSubType){
                if(is_null($subsystemSubType->getId())){
                    $subsystemType->removeSubsystemSubType($subsystemSubType);
                    $subsystemSubType = $subsystemSubTypeRepository->findOneBy(['name' => $subsystemSubType->getName()]);
                    if(!$subsystemType->getSubsystemSubTypes()->contains($subsystemSubType)){
                        $subsystemType->addSubsystemSubType($subsystemSubType);
//                        $subsystemSubType->addSubsystemType($subsystemType);
                    }
                }
            }

            $subsystemTypeRepository->save($subsystemType, true);

            $this->sst = new SubsystemType();
            $this->entity = $this->sst;
            if (!is_null($this->modal)) {
                $this->modalManage($subsystemType, 'Se ha seleccionado el nuevo tipo agregada.', [
                    'subsystemType' => $subsystemType->getId()
                ]);
                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($subsystemType, $successMsg);
                return null;
            }

            $this->addFlash('success', $successMsg);
            return $this->redirectToRoute('app_subsystem_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): ?string
    {
        return 'norender|*';
    }
}
