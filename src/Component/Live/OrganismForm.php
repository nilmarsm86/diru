<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Component\Twig\Modal\Modal;
use App\Entity\Organism;
use App\Form\OrganismType;
use App\Repository\OrganismRepository;
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

#[AsLiveComponent(template: 'component/live/organism_form.html.twig')]
final class OrganismForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?Organism $org = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    public function mount(?organism $org = null): void
    {
        $this->org = (is_null($org)) ? new Organism() : $org;
    }

    /**
     * @param Organism $organism
     * @return void
     */
    private function modalManage(Organism $organism): void
    {
        $template = $this->renderView("partials/_form_success.html.twig", [
            'id' => 'new_' . $this->getClassName($this->org::class) . '_' . $this->org->getId(),
            'type' => 'text-bg-primary',
            'message' => 'Seleccione el nuevo organismo agregado.'
        ]);

        $this->dispatchBrowserEvent('type--entity-plus:update', [
            'data' => [
                'organism' => $organism->getId()
            ],
            'modal' => $this->modal,
            'response' => $template
        ]);

        $this->dispatchBrowserEvent(Modal::MODAL_CLOSE);

        $this->org = new Organism();
        $this->resetForm();//establecer un objeto provincia nuevo
    }

    /**
     * @param string $successMsg
     * @return void
     */
    private function ajaxManage(string $successMsg): void
    {
        $template = $this->renderView("partials/_form_success.html.twig", [
            'id' => 'new_' . $this->getClassName($this->org::class) . '_' . $this->org->getId(),
            'type' => 'text-bg-success',
            'message' => $successMsg
        ]);

        $this->org = new Organism();
        $this->emitSuccess([
            'response' => $template
        ]);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(OrganismType::class, $this->org);
    }

    /**
     * @throws Exception
     */
    #[LiveAction]
    public function save(OrganismRepository $organismRepository): ?Response
    {
        $successMsg = (is_null($this->org->getId())) ? 'Se ha agregado el organismo.' : 'Se ha modificado el organismo.';//TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var Organism $organism */
            $organism = $this->getForm()->getData();

            $organismRepository->save($organism, true);

            if (!is_null($this->modal)) {
                $this->modalManage($organism);
                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($successMsg);
                return null;
            }

            $this->addFlash('success', $successMsg);
            return $this->redirectToRoute('app_organism_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

}
