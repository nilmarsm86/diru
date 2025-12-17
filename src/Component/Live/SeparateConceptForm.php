<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Contract;
use App\Entity\SeparateConcept;
use App\Form\SeparateConceptType;
use App\Repository\ContractRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'component/live/separate_concept_form.html.twig')]
final class SeparateConceptForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?SeparateConcept $sc = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    public function mount(?SeparateConcept $sc = null): void
    {
        $this->sc = (is_null($sc)) ? new SeparateConcept() : $sc;
    }

    /**
     * @return FormInterface<Contract>
     */
    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(SeparateConceptType::class, $this->sc);
    }

    #[LiveAction]
    public function save(ContractRepository $contractRepository): ?Response
    {
        $successMsg = (is_null($this->sc?->getId())) ? 'Se ha agregado el concepto.' : 'Se ha modificado el concepto.'; // TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var SeparateConcept $sc */
            $sc = $this->getForm()->getData();

            $contractRepository->save($sc, true);

            $this->sc = new SeparateConcept();
            if (!is_null($this->modal)) {
                $this->modalManage($sc, 'Se ha seleccionado el nuevo concepto.', [
                    'concept' => $sc->getId(),
                ]);

                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($sc, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('app_separate_concept_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }
}
