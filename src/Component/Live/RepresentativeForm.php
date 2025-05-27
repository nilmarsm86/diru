<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Component\Twig\Modal\Modal;
use App\Entity\Person;
use App\Entity\Representative;
use App\Form\PersonType;
use App\Form\RepresentativeType;
use App\Repository\PersonRepository;
use App\Repository\RepresentativeRepository;
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

#[AsLiveComponent(template: 'component/live/representative_form.html.twig')]
final class RepresentativeForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?Representative $per = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    public function mount(?Representative $per = null): void
    {
        $this->per = (is_null($per)) ? new Representative() : $per;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(RepresentativeType::class, $this->per);
    }

    /**
     * @throws Exception
     */
    #[LiveAction]
    public function save(RepresentativeRepository $representativeRepository): ?Response
    {
        $successMsg = (is_null($this->per->getId())) ? 'Se ha agregado el representante.' : 'Se ha modificado el representante.';//TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var Representative $representative */
            $representative = $this->getForm()->getData();

            $representativeRepository->save($representative, true);

            if (!is_null($this->modal)) {
//                $this->modalManage($representative);
                $this->modalManage($representative, 'Selecione el representante agregado.', [
                    'representative' => $representative->getId()
                ]);
                return null;
            }

            if ($this->ajax) {
//                $this->ajaxManage($successMsg);
                $this->ajaxManage($representative, $successMsg);
                return null;
            }

            $this->addFlash('success', $successMsg);
            return $this->redirectToRoute('app_person_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

}
