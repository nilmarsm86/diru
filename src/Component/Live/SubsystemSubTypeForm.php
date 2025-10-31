<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\SubsystemSubType;
use App\Form\SubsystemSubTypeType;
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
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'component/live/subsystem_sub_type_form.html.twig')]
final class SubsystemSubTypeForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?SubsystemSubType $ssst = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

//    #[LiveProp(writable: true)]
//    public ?string $subsystemType = null;

    protected function instantiateForm(): FormInterface
    {
//        dump($this->formValues);
//        if(!is_null($this->subsystemType)){
//            $this->formValues['subsystemType'] = $this->subsystemType;
//        }

        return $this->createForm(SubsystemSubTypeType::class, $this->ssst, [
            'modal' => $this->modal,
            'screen' => 'subtype'
        ]);
    }

    public function mount(?SubsystemSubType $ssst = null): void
    {
        $this->ssst = $ssst;
        if (is_null($this->ssst)) {
            $this->ssst = new SubsystemSubType();
        } else {
//            if (!is_null($this->ssst->getSubsystemType())) {
//                $this->subsystemType = (string)$this->ssst->getSubSystemType()->getId();
//            }
        }
    }

    /**
     * @throws Exception
     */
    #[LiveAction]
    public function save(SubsystemSubTypeRepository $subsystemSubTypeRepository, SubsystemTypeRepository $subsystemTypeRepository): ?Response
    {
        $successMsg = (is_null($this->ssst->getId())) ? 'Se ha agregado el sub tipo.' : 'Se ha modificado el sub tipo.';//TODO: personalizar los mensajes
        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var SubsystemSubType $subsystemSubType */
            $subsystemSubType = $this->getForm()->getData();

//            $subsystemType = $subsystemTypeRepository->find($this->subsystemType);
//            $subsystemSubType->setSubsystemType($subsystemType);

            $subsystemSubTypeRepository->save($subsystemSubType, true);

            $this->ssst = new SubsystemSubType();
            if (!is_null($this->modal)) {
                $this->modalManage($subsystemSubType, 'Se ha seleccionado el nuevo subtipo agregado.', [
                    'subsystemSubType' => $subsystemSubType->getId()
                ]);
                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($subsystemSubType, $successMsg);
                return null;
            }

            $this->addFlash('success', $successMsg);
            return $this->redirectToRoute('app_subsystem_sub_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): ?string
    {
        return 'norender|*';
    }

}
