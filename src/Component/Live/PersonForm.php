<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Person;
use App\Form\PersonType;
use App\Repository\PersonRepository;
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

#[AsLiveComponent(template: 'component/live/person_form.html.twig')]
final class PersonForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?Person $per = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    public function mount(?Person $per = null): void
    {
        $this->per = (is_null($per)) ? new Person() : $per;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(PersonType::class, $this->per);
    }

    /**
     * @throws Exception
     */
    #[LiveAction]
    public function save(PersonRepository $personRepository): ?Response
    {
        $successMsg = (is_null($this->per->getId())) ? 'Se ha agregado el representante.' : 'Se ha modificado el representante.';//TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var Person $person */
            $person = $this->getForm()->getData();

            $personRepository->save($person, true);

            $this->per = new Person();
            if (!is_null($this->modal)) {
                $this->modalManage($person, 'Se ha selecionado la persona agregada.', [
                    'person' => $person->getId()
                ]);
                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($person, $successMsg);
                return null;
            }

            $this->addFlash('success', $successMsg);
            return $this->redirectToRoute('app_person_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

}
