<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Ite;
use App\Form\IteType;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use App\Repository\IteRepository;
use App\Repository\IteSourceRepository;
use App\Repository\MeasurementUnitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent(template: 'component/live/ite_form.html.twig')]
final class IteForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;
    use LiveCollectionTrait;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?Ite $indicator = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp]
    public Ite $entity;

    #[LiveProp]
    public \App\Entity\Enums\IteType $type;

    #[LiveProp(writable: true)]
    public ?int $measurementUnit = null;

    #[LiveProp(writable: true)]
    public int $country = 0;

    #[LiveProp(writable: true)]
    public int $city = 0;

    #[LiveProp(writable: true)]
    public ?int $iteSource = null;

    public function __construct(
        protected readonly CountryRepository $countryRepository,
        protected readonly CityRepository $cityRepository,
    ) {
    }

    public function applyIteSource(): void
    {
        if (!is_null($this->iteSource)) {
            $this->formValues['source'] = $this->iteSource;
        }
    }

    public function applyMeasurementUnit(): void
    {
        if (!is_null($this->measurementUnit)) {
            $this->formValues['measurementUnit'] = $this->measurementUnit;
        }
    }

    private function applyCityCountryField(string $field, int $value): void
    {
        if (0 === $value) {
            return;
        }

        /** @var array<string, mixed> $cityCountry */
        $cityCountry = $this->formValues['cityCountry'] ?? [];
        $cityCountry[$field] = (string) $value;

        $this->formValues['cityCountry'] = $cityCountry;
        $this->{$field} = 0;
    }

    public function mount(?Ite $ite = null): void
    {
        $this->indicator = $ite;

        if (is_null($this->indicator)) {
            $this->indicator = new Ite();
        } else {
            if (!is_null($this->indicator->getMeasurementUnit())) {
                $this->measurementUnit = $this->indicator->getMeasurementUnit()->getId();
            }

            if (!is_null($this->indicator->getSource())) {
                $this->iteSource = $this->indicator->getSource()->getId();
            }
        }
        $this->entity = $this->indicator;
    }

    public function preValue(): void
    {
        $this->applyMeasurementUnit();
        $this->applyIteSource();
        $this->applyCityCountryField('country', $this->country);
        $this->applyCity();
    }

    private function applyCity(): void
    {
        if (0 !== $this->city) {
            $this->applyCityCountryField('city', $this->city);
            $this->city = 0;

            return;
        }

        $this->reconcileCityWithCountry();
    }

    private function reconcileCityWithCountry(): void
    {
        /** @var array<string, array<string, mixed>> $formValues */
        $formValues = $this->formValues;
        $cityCountry = $formValues['cityCountry'] ?? [];
        $countryId = $cityCountry['country'] ?? null;

        if (null === $countryId) {
            return;
        }

        $cityId = $cityCountry['city'] ?? null;

        if ((bool) $cityId) {
            $this->reconcileExistingCity($cityId, $countryId);

            return;
        }

        $this->setFirstCityOfCountry($countryId);
    }

    private function reconcileExistingCity(mixed $cityId, mixed $countryId): void
    {
        $cit = $this->cityRepository->find($cityId);

        if ((string) $cit?->getCountry()?->getId() === $countryId) {
            return;
        }

        $this->setFirstCityOfCountry($countryId);
    }

    private function setFirstCityOfCountry(mixed $countryId): void
    {
        $coun = $this->countryRepository->find($countryId);

        if (null === $coun) {
            return;
        }

        $cities = $coun->getCities();
        $first = $cities->count() > 0 ? $cities->first() : false;

        /** @var array<string, mixed> $cityCountry */
        $cityCountry = $this->formValues['cityCountry'] ?? [];

        $cityCountry['city'] = false !== $first ? (string) $first->getId() : '';

        $this->formValues['cityCountry'] = $cityCountry;
    }

    /**
     * @return FormInterface<Ite>
     */
    protected function instantiateForm(): FormInterface
    {
        $this->preValue();

        /** @var array<string, array<string, mixed>> $formValues */
        $formValues = $this->formValues;
        $country = 0;
        $city = 0;

        if (null === $this->indicator?->getId()) {
            if (isset($formValues['cityCountry'])) {
                /** @var int $country */
                $country = $formValues['cityCountry']['country'] ?? 0;
                /** @var int $city */
                $city = $formValues['cityCountry']['city'] ?? 0;
            }
        } else {
            $cit = $this->indicator->getCity();
            /** @var int $country */
            $country = $formValues['cityCountry']['country'] ?? $cit?->getCountry()?->getId();
            /** @var int $city */
            $city = $formValues['cityCountry']['city'] ?? $cit?->getId();
        }

        return $this->createForm(IteType::class, $this->indicator, [
            'country' => (int) $country,
            'city' => (int) $city,
            'live_form' => ('on(change)|*' === $this->getDataModelValue()),
            'modal' => $this->modal,
        ]);
    }

    #[LiveAction]
    public function save(
        IteRepository $iteRepository,
        MeasurementUnitRepository $measurementUnitRepository,
        IteSourceRepository $iteSourceRepository,
    ): ?Response {
        $this->preValue();
        /** @var array<string, array<string, mixed>> $formValues */
        $formValues = $this->formValues;

        $successMsg = (is_null($this->indicator?->getId())) ? 'Se ha agregado el ITE.' : 'Se ha modificado el ITE.'; // TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var Ite $ite */
            $ite = $this->getForm()->getData();

            $ite->setType($this->type);

            $measurementUnit = $measurementUnitRepository->find($formValues['measurementUnit']);
            $ite->setMeasurementUnit($measurementUnit);

            $iteSource = $iteSourceRepository->find($formValues['source']);
            $ite->setSource($iteSource);

            /** @var array<string, array<string, mixed>> $formValues */
            $formValues = $this->formValues;
            $city = $this->cityRepository->find($formValues['cityCountry']['city']);
            $ite->setCity($city);

            $iteRepository->save($ite, true);

            $this->indicator = new Ite();
            $this->entity = $this->indicator;
            if (!is_null($this->modal)) {
                $this->modalManage($ite, 'Se ha seleccionado el ITE agregado.', [
                    'ite' => $ite->getId(),
                ]);

                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($ite, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('ite_upload_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    //    /** @SuppressWarnings(PHPMD.UnusedPrivateMethod) */
    //    private function getDataModelValue(): string
    //    {
    //        return 'norender|*';
    //    }
}
