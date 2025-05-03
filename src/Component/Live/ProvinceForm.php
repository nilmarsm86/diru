<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Component\Twig\Modal\Modal;
use App\Entity\Province;
use App\Form\ProvinceType;
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

#[AsLiveComponent(template: 'component/live/province_form.html.twig')]
final class ProvinceForm extends AbstractController
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
    public ?Province $prov = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    public function mount(?Province $prov = null): void
    {
        $this->prov = (is_null($prov)) ? new Province() : $prov;
    }

    /**
     * @param Province $province
     * @return void
     */
    public function modalManage(Province $province): void
    {
        $template = $this->renderView("partials/_form_success.html.twig", [
            'id' => 'new_' . $this->getClassName($this->prov::class) . '_' . $this->prov->getId(),
            'type' => 'text-bg-primary',
            'message' => 'Seleccione la nueva provincia agregada.'
        ]);

        $this->dispatchBrowserEvent('type--entity-plus:update', [
            'data' => [
                'province' => $province->getId()
            ],
            'modal' => $this->modal,
            'response' => $template
        ]);

        $this->dispatchBrowserEvent(Modal::MODAL_CLOSE);

        $this->prov = new Province();
        $this->resetForm();//establecer un objeto provincia nuevo
    }

    /**
     * @param string $successMsg
     * @return void
     */
    public function ajaxManage(string $successMsg): void
    {
        $template = $this->renderView("partials/_form_success.html.twig", [
            'id' => 'new_' . $this->getClassName($this->prov::class) . '_' . $this->prov->getId(),
            'type' => 'text-bg-success',
            'message' => $successMsg
        ]);

        $this->prov = new Province();
        $this->emitSuccess([
            'response' => $template
        ]);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ProvinceType::class, $this->prov);
    }

    /**
     * @throws Exception
     */
    #[LiveAction]
    public function save(ProvinceRepository $provinceRepository): ?Response
    {
        $successMsg = (is_null($this->prov->getId())) ? 'Se ha agregado la provincia.' : 'Se ha modificado la provincia.';//TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var Province $province */
            $province = $this->getForm()->getData();

            $provinceRepository->save($province, true);

            if (!is_null($this->modal)) {
                $this->modalManage($province);
                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($successMsg);
                return null;
            }

            $this->addFlash('success', $successMsg);
            return $this->redirectToRoute('app_province_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

//    protected function getSuccessFormEventName(): string
//    {
//        return self::FORM_SUCCESS;
//    }


}
