<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Component\Twig\Modal\Modal;
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

    /**
     * @param Person $person
     * @return void
     */
    private function modalManage(Person $person): void
    {
        $template = $this->renderView("partials/_form_success.html.twig", [
            'id' => 'new_' . $this->getClassName($this->per::class) . '_' . $this->per->getId(),
            'type' => 'text-bg-primary',
            'message' => 'Selecione el representante agregado.'
        ]);

        $this->dispatchBrowserEvent('type--entity-plus:update', [
            'data' => [
                'person' => $person->getId()
            ],
            'modal' => $this->modal,
            'response' => $template
        ]);

        $this->dispatchBrowserEvent(Modal::MODAL_CLOSE);

        $this->per = new Person();
        $this->resetForm();//establecer un objeto provincia nuevo
    }

    /**
     * @param string $successMsg
     * @return void
     */
    private function ajaxManage(string $successMsg): void
    {
        $template = $this->renderView("partials/_form_success.html.twig", [
            'id' => 'new_' . $this->getClassName($this->per::class) . '_' . $this->per->getId(),
            'type' => 'text-bg-success',
            'message' => $successMsg
        ]);

        $this->per = new Person();
        $this->emitSuccess([
            'response' => $template
        ]);
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

            if (!is_null($this->modal)) {
                $this->modalManage($person);
                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($successMsg);
                return null;
            }

            $this->addFlash('success', $successMsg);
            return $this->redirectToRoute('app_person_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

}
