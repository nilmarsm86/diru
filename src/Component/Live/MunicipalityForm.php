<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Component\Twig\Modal\Modal;
use App\Entity\Municipality;
use App\Form\MunicipalityType;
use App\Repository\MunicipalityRepository;
use App\Repository\ProvinceRepository;
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

#[AsLiveComponent(template: 'component/live/municipality_form.html.twig')]
final class MunicipalityForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

//    const FORM_SUCCESS = self::class . '_form_success';

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
     * @param string $successMsg
     * @return void
     */
    public function ajaxManage(string $successMsg): void
    {
        $template = $this->renderView("partials/_form_success.html.twig", [
            'id' => 'new_' . $this->getClassName($this->mun::class) . '_' . $this->mun->getId(),
            'type' => 'text-bg-success',
            'message' => $successMsg
        ]);

        $this->mun = new Municipality();
        $this->emitSuccess([
            'response' => $template
        ]);
    }

    /**
     * @param Municipality $municipality
     * @return void
     */
    public function modalManage(Municipality $municipality): void
    {
        $template = $this->renderView("partials/_form_success.html.twig", [
            'id' => 'new_' . $this->getClassName($this->mun::class) . '_' . $this->mun->getId(),
            'type' => 'text-bg-primary',
            'message' => 'Seleccione el nuevo municipio agregado.'
        ]);

        $this->dispatchBrowserEvent('type--entity-plus:update', [
            'data' => [
                'municipality' => $municipality->getId()
            ],
            'modal' => $this->modal,
            'response' => $template
        ]);

        $this->dispatchBrowserEvent(Modal::MODAL_CLOSE);

        $this->mun = new Municipality();
        $this->resetForm();//establecer un objeto provincia nuevo
    }

    protected function instantiateForm(): FormInterface
    {
        if(!is_null($this->province)){
            $this->formValues['province'] = $this->province;
        }

        return $this->createForm(MunicipalityType::class, $this->mun, [
            'modal' => $this->modal
        ]);
    }

    public function mount(?Municipality $mun = null): void
    {
        $this->mun = $mun;
        if (is_null($this->mun)) {
            $this->mun = new Municipality();
        } else {
            if (!is_null($this->mun->getProvince())) {
                $this->province = (string)$this->mun->getProvince()->getId();
            }
        }
    }

    /**
     * @throws Exception
     */
    #[LiveAction]
    public function save(MunicipalityRepository $municipalityRepository, ProvinceRepository $provinceRepository): ?Response
    {
        $successMsg = (is_null($this->mun->getId())) ? 'Se ha agregado el municipio.' : 'Se ha modificado el municipio.';//TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var Municipality $municipality */
            $municipality = $this->getForm()->getData();
            $province = $provinceRepository->find($this->province);
            $municipality->setProvince($province);

            $municipalityRepository->save($municipality, true);

            if (!is_null($this->modal)) {
                $this->modalManage($municipality);
                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($successMsg);
                return null;
            }

            $this->addFlash('success', $successMsg);
            return $this->redirectToRoute('app_municipality_index', [], Response::HTTP_SEE_OTHER);
//            return null;
        }

        return null;
    }

//    protected function getSuccessFormEventName(): string
//    {
//        return self::FORM_SUCCESS;
//    }


}
