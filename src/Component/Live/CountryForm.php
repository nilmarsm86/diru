<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Country;
use App\Form\CountryType;
use App\Repository\CountryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent(template: 'component/live/country_form.html.twig')]
final class CountryForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentToolsTrait;
    use ComponentForm;
    use LiveCollectionTrait;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?Country $cou = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp]
    public Country $entity;

    public function mount(?Country $prov = null): void
    {
        $this->cou = (is_null($prov)) ? new Country() : $prov;
        $this->entity = $this->cou;
    }

    /**
     * @return FormInterface<Country>
     */
    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(CountryType::class, $this->cou);
    }

    #[LiveAction]
    public function save(CountryRepository $countryRepository): ?Response
    {
        $successMsg = (is_null($this->cou?->getId())) ? 'Se ha agregado el país.' : 'Se ha modificado el país.'; // TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var Country $country */
            $country = $this->getForm()->getData();

            $countryRepository->save($country, true);

            $this->cou = new Country();
            $this->entity = $this->cou;
            if (!is_null($this->modal)) {
                $this->modalManage($country, 'Se ha seleccionado el nuevo país agregado.', [
                    'country' => $country->getId(),
                ]);

                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($country, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('app_country_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    /** @SuppressWarnings(PHPMD.UnusedPrivateMethod) */
    private function getDataModelValue(): string
    {
        return 'norender|*';
    }
}
