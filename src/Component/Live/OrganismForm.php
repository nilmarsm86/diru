<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Organism;
use App\Form\OrganismType;
use App\Repository\OrganismRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'partials/live_component/_only_name_form.html.twig')]
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

    #[LiveProp]
    public Organism $entity;

    public function mount(?Organism $org = null): void
    {
        $this->org = (is_null($org)) ? new Organism() : $org;
        $this->entity = $this->org;
    }

    /**
     * @return FormInterface<Organism>
     */
    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(OrganismType::class, $this->org);
    }

    /**
     * @throws \Exception
     */
    #[LiveAction]
    public function save(OrganismRepository $organismRepository): ?Response
    {
        $successMsg = (is_null($this->org?->getId())) ? 'Se ha agregado el organismo.' : 'Se ha modificado el organismo.'; // TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var Organism $organism */
            $organism = $this->getForm()->getData();

            $organismRepository->save($organism, true);

            $this->org = new Organism();
            $this->entity = $this->org;
            if (!is_null($this->modal)) {
                $this->modalManage($organism, 'Se ha seleccionado el nuevo organismo agregado.', [
                    'organism' => $organism->getId(),
                ]);

                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($organism, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('app_organism_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }
}
