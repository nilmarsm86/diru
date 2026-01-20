<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Municipality;
use App\Form\MunicipalityType;
use App\Repository\MunicipalityRepository;
use App\Repository\ProvinceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'component/live/municipality_form.html.twig')]
final class MunicipalityForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?Municipality $mun = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp(writable: true)]
    public ?string $province = null;

    /**
     * @return FormInterface<Municipality>
     */
    protected function instantiateForm(): FormInterface
    {
        if (!is_null($this->province)) {
            $this->formValues['province'] = $this->province;
        }

        return $this->createForm(MunicipalityType::class, $this->mun, [
            'modal' => $this->modal,
        ]);
    }

    public function mount(?Municipality $mun = null): void
    {
        $this->mun = $mun;
        if (is_null($this->mun)) {
            $this->mun = new Municipality();
        } else {
            if (!is_null($this->mun->getProvince())) {
                $this->province = (string) $this->mun->getProvince()->getId();
            }
        }
    }

    #[LiveAction]
    public function save(MunicipalityRepository $municipalityRepository, ProvinceRepository $provinceRepository): ?Response
    {
        $successMsg = (is_null($this->mun?->getId())) ? 'Se ha agregado el municipio.' : 'Se ha modificado el municipio.'; // TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var Municipality $municipality */
            $municipality = $this->getForm()->getData();
            $province = $provinceRepository->find($this->province);
            $municipality->setProvince($province);

            $municipalityRepository->save($municipality, true);

            $this->mun = new Municipality();
            if (!is_null($this->modal)) {
                $this->modalManage($municipality, 'Se ha seleccionado el nuevo municipio agregado.', [
                    'municipality' => $municipality->getId(),
                ]);

                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($municipality, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('app_municipality_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): string
    {
        return 'norender|*';
    }
}
