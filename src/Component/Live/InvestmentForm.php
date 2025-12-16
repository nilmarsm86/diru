<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Investment;
use App\Form\InvestmentType;
use App\Repository\InvestmentRepository;
use App\Repository\LocationZoneRepository;
use App\Repository\MunicipalityRepository;
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

#[AsLiveComponent(template: 'component/live/investment_form.html.twig')]
final class InvestmentForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentToolsTrait;
    use ComponentForm;
    use LiveCollectionTrait;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?Investment $inv = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp(writable: true)]
    public ?int $locationZone = 0;

    #[LiveProp(writable: true)]
    public ?string $street = '';

    #[LiveProp(writable: true)]
    public int $province = 0;

    #[LiveProp(writable: true)]
    public int $municipality = 0;

    public function __construct(
        protected readonly ProvinceRepository $provinceRepository,
        protected readonly MunicipalityRepository $municipalityRepository,
    ) {
    }

    public function mount(?Investment $inv = null): void
    {
        $this->inv = $inv;
        if (is_null($this->inv)) {
            $this->inv = new Investment();
        }
    }

    public function preValue(): void
    {
        /** @var array<string, array<string, mixed>> $formValues */
        $formValues = $this->formValues;

        if (0 !== $this->locationZone) {
            $formValues['locationZone'] = (string) $this->locationZone;
            $this->locationZone = 0;
            $this->formValues = $formValues;
        }

        if ('' !== $this->street) {
            /** @var array<string, mixed> $streetAddress */
            $streetAddress = $formValues['streetAddress'] ?? [];
            $streetAddress['street'] = (string) $this->street;
            $formValues['streetAddress'] = $streetAddress;
            $this->street = '';
            $this->formValues = $formValues;
        }

        if (0 !== $this->province) {
            /** @var array<string, mixed> $streetAddress */
            $streetAddress = $formValues['streetAddress'] ?? [];
            /** @var array<string, mixed> $address */
            $address = $streetAddress['address'] ?? [];
            $address['province'] = (string) $this->province;
            $streetAddress['address'] = $address;
            $formValues['streetAddress'] = $streetAddress;
            $this->province = 0;
            $this->formValues = $formValues;
        }

        if (0 !== $this->municipality) {
            /** @var array<string, mixed> $streetAddress */
            $streetAddress = $formValues['streetAddress'] ?? [];
            /** @var array<string, mixed> $address */
            $address = $streetAddress['address'] ?? [];
            $address['municipality'] = (string) $this->municipality;
            $streetAddress['address'] = $address;
            $formValues['streetAddress'] = $streetAddress;
            $this->municipality = 0;
            $this->formValues = $formValues;
        } else {
            /** @var array<string, array<string, array<int, mixed>>> $formValues */
            $formValues = $this->formValues;
            if (isset($formValues['streetAddress']['address'])) {
                if (isset($formValues['streetAddress']['address']['province'])) {
                    if ($formValues['streetAddress']['address']['municipality']) {
                        $mun = $this->municipalityRepository->find($formValues['streetAddress']['address']['municipality']);
                        if ((string) $mun?->getProvince()?->getId() !== $formValues['streetAddress']['address']['province']) {
                            $prov = $this->provinceRepository->find($formValues['streetAddress']['address']['province']);
                            if (!is_null($prov)) {
                                $formValues['streetAddress']['address']['municipality'] = ($prov->getMunicipalities()->count() > 0 && false !== $prov->getMunicipalities()->first())
                                    ? (string) $prov->getMunicipalities()->first()->getId()
                                    : '';
                            }
                        }
                    } else {
                        $prov = $this->provinceRepository->find($formValues['streetAddress']['address']['province']);
                        if (!is_null($prov)) {
                            if ($prov->getMunicipalities()->count() > 0 && false !== $prov->getMunicipalities()->first()) {
                                $formValues['streetAddress']['address']['municipality'] = (string) $prov->getMunicipalities()->first()->getId();
                            }
                        }
                    }
                }
            }

            $this->formValues = $formValues;
        }
    }

    /**
     * @return FormInterface<Investment>
     */
    protected function instantiateForm(): FormInterface
    {
        $this->preValue();
        $province = 0;
        $municipality = 0;
        /** @var array<string, array<string, array<string, mixed>>> $formValues */
        $formValues = $this->formValues;
        if (null === $this->inv?->getId()) {
            if (isset($formValues['streetAddress']['address'])) {
                /** @var int $province */
                $province = $formValues['streetAddress']['address']['province'] ?? 0;
                /** @var int $municipality */
                $municipality = $formValues['streetAddress']['address']['municipality'] ?? 0;
            }
            if (isset($formValues['streetAddress']['street'])) {
                $street = $formValues['streetAddress']['street'];
            }
        } else {
            $mun = $this->inv->getMunicipality();
            /** @var int $province */
            $province = $formValues['streetAddress']['address']['province'] ?? $mun?->getProvince()?->getId();
            /** @var int $municipality */
            $municipality = $formValues['streetAddress']['address']['municipality'] ?? $mun?->getId();
            /** @var string $street */
            $street = $formValues['streetAddress']['street'] ?? $this->inv->getStreet();
        }

        return $this->createForm(InvestmentType::class, $this->inv, [
            'street' => $street ?? '',
            'province' => (int) $province,
            'municipality' => (int) $municipality,
            'live_form' => ('on(change)|*' === $this->getDataModelValue()),
            'modal' => $this->modal,
        ]);
    }

    #[LiveAction]
    public function save(InvestmentRepository $investmentRepository, LocationZoneRepository $locationZoneRepository): ?Response
    {
        $this->preValue();

        /** @var array<string, array<string, mixed>> $formValues */
        $formValues = $this->formValues;

        $successMsg = (is_null($this->inv?->getId())) ? 'Se ha agregado la inversión.' : 'Se ha modificado la inversión.'; // TODO: personalizar los mensajes

        $this->submitForm();
        if ($this->isSubmitAndValid()) {
            /** @var Investment $inv */
            $inv = $this->getForm()->getData();

            /** @var array<string, mixed> $streetAddress */
            $streetAddress = $formValues['streetAddress'] ?? [];
            /** @var string $street */
            $street = $streetAddress['street'] ?? '';
            $inv->setStreet($street);

            $locationZone = $locationZoneRepository->find($formValues['locationZone']);
            $inv->setLocationZone($locationZone);

            /** @var array<string, array<string, array<string, mixed>>> $formValues */
            $formValues = $this->formValues;
            $municipality = $this->municipalityRepository->find($formValues['streetAddress']['address']['municipality']);
            $inv->setMunicipality($municipality);

            $investmentRepository->save($inv, true);

            $this->inv = new Investment();
            if (!is_null($this->modal)) {
                $this->modalManage($inv, 'Se ha seleccionado la nueva inversión agregada.', [
                    'investment' => $inv->getId(),
                ]);

                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($inv, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('app_investment_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): string
    {
        return 'norender|*';
    }
}
