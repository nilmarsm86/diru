<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\IndividualClient;
use App\Entity\Person;
use App\Form\IndividualClientType;
use App\Repository\IndividualClientRepository;
use App\Repository\MunicipalityRepository;
use App\Repository\ProvinceRepository;
use App\Repository\RepresentativeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'component/live/individual_client_form.html.twig')]
final class IndividualClientForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?IndividualClient $ic = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp(writable: true)]
    public ?string $street = '';

    #[LiveProp(writable: true)]
    public int $province = 0;

    #[LiveProp(writable: true)]
    public int $municipality = 0;

    #[LiveProp(writable: true)]
    public int $representative = 0;

    public function __construct(
        protected readonly ProvinceRepository $provinceRepository,
        protected readonly MunicipalityRepository $municipalityRepository,
        //        protected readonly PersonRepository       $personRepository
    ) {
    }

    public function mount(?IndividualClient $ic = null): void
    {
        $this->ic = $ic;
        if (is_null($this->ic)) {
            $this->ic = new IndividualClient();
        }
    }

    public function createPerson(IndividualClient $individualClient): Person
    {
        /** @var array<string, array<string, mixed>> $formValues */
        $formValues = $this->formValues;
        $person = $individualClient->getPerson();
        if (null === $person) {
            $person = new Person();
        }

        /** @var string $name */
        $name = $formValues['person']['name'];
        $person->setName($name);

        /** @var string $lastname */
        $lastname = $formValues['person']['lastname'];
        $person->setLastname($lastname);

        /** @var string $identificationNumber */
        $identificationNumber = $formValues['person']['identificationNumber'];
        $person->setIdentificationNumber($identificationNumber);

        /** @var string $passport */
        $passport = $formValues['person']['passport'];
        $person->setPassport(false === (bool) $passport ? null : $passport);

        return $person;
    }

    public function preValue(): void
    {
        /** @var array<string, array<string, mixed>> $formValues */
        $formValues = $this->formValues;

        if (0 !== $this->representative) {
            $formValues['representative'] = (string) $this->representative;
            $this->representative = 0;
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
     * @return FormInterface<IndividualClient>
     */
    protected function instantiateForm(): FormInterface
    {
        $this->preValue();
        $province = 0;
        $municipality = 0;
        /** @var array<string, array<string, array<string, mixed>>> $formValues */
        $formValues = $this->formValues;
        if (null === $this->ic?->getId()) {
            if (isset($formValues['streetAddress']['address'])) {
                /** @var int $province */
                $province = '' === $formValues['streetAddress']['address']['province'] ? 0 : $formValues['streetAddress']['address']['province'];
                /** @var int $municipality */
                $municipality = '' === $formValues['streetAddress']['address']['municipality'] ? 0 : $formValues['streetAddress']['address']['municipality'];
            }
            if (isset($formValues['streetAddress']['street'])) {
                $street = $formValues['streetAddress']['street'];
            }
        } else {
            $mun = $this->ic->getMunicipality();
            /** @var int $province */
            $province = (false === (bool) $formValues['streetAddress']['address']['province'] ? $mun?->getProvince()?->getId() : $formValues['streetAddress']['address']['province']);
            /** @var int $municipality */
            $municipality = (false === (bool) $formValues['streetAddress']['address']['municipality'] ? $mun?->getId() : $formValues['streetAddress']['address']['municipality']);
            /** @var string $street */
            $street = (false === (bool) $formValues['streetAddress']['street'] ? $this->ic->getStreet() : $formValues['streetAddress']['street']);
        }

        return $this->createForm(IndividualClientType::class, $this->ic, [
            'street' => $street ?? '',
            'province' => (int) $province,
            'municipality' => (int) $municipality,
            'live_form' => ('on(change)|*' === $this->getDataModelValue()),
        ]);
    }

    #[LiveAction]
    public function save(IndividualClientRepository $individualClientRepository, RepresentativeRepository $representativeRepository): ?Response
    {
        $this->preValue();

        /** @var array<string, array<string, mixed>> $formValues */
        $formValues = $this->formValues;

        $successMsg = (is_null($this->ic?->getId())) ? 'Se ha agregado el cliente.' : 'Se ha modificado el cliente.'; // TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var IndividualClient $ic */
            $ic = $this->getForm()->getData();

            $person = $this->createPerson($ic);
            $ic->setPerson($person);

            $representative = $representativeRepository->find($formValues['representative']);
            $ic->setRepresentative($representative);

            /** @var string $street */
            $street = $formValues['streetAddress']['street'];
            $ic->setStreet($street);

            /** @var array<string, array<string, array<string, mixed>>> $formValues */
            $formValues = $this->formValues;
            $municipality = $this->municipalityRepository->find($formValues['streetAddress']['address']['municipality']);
            $ic->setMunicipality($municipality);

            $individualClientRepository->save($ic, true);

            $this->ic = new IndividualClient();
            if (!is_null($this->modal)) {
                $this->modalManage($ic, 'Se ha seleccionado el nuevo cliente personal agregado.', [
                    'individualClient' => $ic->getId(),
                ], 'text-bg-success');

                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($ic, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('app_individual_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): string
    {
        return 'norender|*';
    }
}
