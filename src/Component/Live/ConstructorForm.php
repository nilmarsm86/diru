<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Constructor;
use App\Form\ConstructorType;
use App\Repository\ConstructorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'component/live/constructor_form.html.twig')]
final class ConstructorForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?Constructor $cons = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public ?string $screen = null;

    #[LiveProp]
    public bool $ajax = false;

    public function mount(?Constructor $cons = null, string $screen = 'project'): void
    {
        $this->cons = (is_null($cons)) ? new Constructor() : $cons;
        $this->screen = $screen;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ConstructorType::class, $this->cons);
    }

    /**
     * @throws \Exception
     */
    #[LiveAction]
    public function save(RequestStack $request, ConstructorRepository $constructorRepository): ?Response
    {
        $successMsg = (is_null($this->cons?->getId())) ? 'Se ha agregado la constructora.' : 'Se ha modificado la constructora.';

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var Constructor $constructor */
            $constructor = $this->getForm()->getData();

            $constructorRepository->save($constructor, true);

            $this->cons = new Constructor();
            if (!is_null($this->modal)) {
                if ('building' === $this->screen) {
                    $this->modalManage($constructor, 'Se ha seleccionado la nueva constructora agregada.', [
                        'constructor' => $constructor->getId(),
                    ]);
                }

                if ('project' === $this->screen) {
                    $this->modalManage($constructor, 'Se ha agregado una nueva constructora, seleccionela.', [
                        'constructor' => $constructor->getId(),
                    ], 'text-bg-success');
                }

                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($constructor, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('app_constructor_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): string
    {
        return 'norender|*';
    }
}
