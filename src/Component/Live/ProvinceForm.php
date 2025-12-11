<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Province;
use App\Form\ProvinceType;
use App\Repository\ProvinceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent(template: 'component/live/province_form.html.twig')]
final class ProvinceForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentToolsTrait;
    use ComponentForm;
    use LiveCollectionTrait;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?Province $prov = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp]
    public Province $entity;

    public function mount(?Province $prov = null): void
    {
        $this->prov = (is_null($prov)) ? new Province() : $prov;
        $this->entity = $this->prov;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ProvinceType::class, $this->prov);
    }

    /**
     * @throws \Exception
     */
    #[LiveAction]
    public function save(ProvinceRepository $provinceRepository): ?Response
    {
        $successMsg = (is_null($this->prov?->getId())) ? 'Se ha agregado la provincia.' : 'Se ha modificado la provincia.'; // TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var Province $province */
            $province = $this->getForm()->getData();

            $provinceRepository->save($province, true);

            $this->prov = new Province();
            $this->entity = $this->prov;
            if (!is_null($this->modal)) {
                $this->modalManage($province, 'Se ha seleccionado la nueva provincia agregada.', [
                    'province' => $province->getId(),
                ]);

                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($province, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('app_province_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): string
    {
        return 'norender|*';
    }
}
