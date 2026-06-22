<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\City;
use App\Form\CityType;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'component/live/city_form.html.twig')]
final class CityForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?City $cit = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp(writable: true)]
    public ?string $country = null;

    /**
     * @return FormInterface<City>
     */
    protected function instantiateForm(): FormInterface
    {
        if (!is_null($this->country)) {
            $this->formValues['country'] = $this->country;
        }

        return $this->createForm(CityType::class, $this->cit, [
            'modal' => $this->modal,
        ]);
    }

    public function mount(?City $cit = null): void
    {
        $this->cit = $cit;
        if (is_null($this->cit)) {
            $this->cit = new City();
        } else {
            if (!is_null($this->cit->getCountry())) {
                $this->country = (string) $this->cit->getCountry()->getId();
            }
        }

        dump($this->cit);
    }

    #[LiveAction]
    public function save(CityRepository $cityRepository, CountryRepository $countryRepository): ?Response
    {
        $successMsg = (is_null($this->cit?->getId())) ? 'Se ha agregado la ciudad.' : 'Se ha modificado la ciudad.'; // TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var City $city */
            $city = $this->getForm()->getData();
            $country = $countryRepository->find($this->country);
            $city->setCountry($country);

            $cityRepository->save($city, true);

            $this->cit = new City();
            if (!is_null($this->modal)) {
                $this->modalManage($city, 'Se ha seleccionado la nueva ciudad agregado.', [
                    'city' => $city->getId(),
                ]);

                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($city, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('app_city_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    /** @SuppressWarnings(PHPMD.UnusedPrivateMethod) */
    private function getDataModelValue(): string
    {
        return 'norender|*';
    }
}
