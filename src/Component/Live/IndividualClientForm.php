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
        if (!$person = $individualClient->getPerson()) {
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
        $person->setPassport(empty($passport) ? null : $passport);

        return $person;
    }

    public function preValue(): void
    {
        /** @var array<string, array<string, array<string, mixed>>> $formValues */
        $formValues = $this->formValues;

        if (0 !== $this->representative) {
            $formValues['representative'] = (string) $this->representative;
            $this->representative = 0;
            $this->formValues = $formValues;
        }

        if ('' !== $this->street) {
            $formValues['streetAddress']['street'] = (string) $this->street;
            $this->street = '';
            $this->formValues = $formValues;
        }

        if (0 !== $this->province) {
            $formValues['streetAddress']['address']['province'] = (string) $this->province;
            $this->province = 0;
            $this->formValues = $formValues;
        }

        if (0 !== $this->municipality) {
            $formValues['streetAddress']['address']['municipality'] = (string) $this->municipality;
            $this->municipality = 0;
            $this->formValues = $formValues;
        } else {
            if (isset($this->formValues['streetAddress']) && isset($this->formValues['streetAddress']['address'])) {
                if (isset($this->formValues['streetAddress']['address']['province'])) {
                    if ($this->formValues['streetAddress']['address']['municipality']) {
                        $mun = $this->municipalityRepository->find((int) $this->formValues['streetAddress']['address']['municipality']);
                        if ((string) $mun?->getProvince()?->getId() !== $this->formValues['streetAddress']['address']['province']) {
                            $prov = $this->provinceRepository->find((int) $this->formValues['streetAddress']['address']['province']);
                            if (!is_null($prov)) {
                                $this->formValues['streetAddress']['address']['municipality'] = ($prov->getMunicipalities()->count() && $prov->getMunicipalities()->first())
                                    ? (string) $prov->getMunicipalities()->first()->getId()
                                    : '';
                            }
                        }
                    } else {
                        $prov = $this->provinceRepository->find((int) $this->formValues['streetAddress']['address']['province']);
                        if (!is_null($prov)) {
                            if ($prov->getMunicipalities()->count() && $prov->getMunicipalities()->first()) {
                                $this->formValues['streetAddress']['address']['municipality'] = (string) $prov->getMunicipalities()->first()->getId();
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @return FormInterface<IndividualClient>
     */
    protected function instantiateForm(): FormInterface
    {
        $this->preValue();

        if (!$this->ic?->getId()) {
            if (isset($this->formValues['streetAddress']) && isset($this->formValues['streetAddress']['address'])) {
                $province = (int) $this->formValues['streetAddress']['address']['province'];
                $municipality = (int) $this->formValues['streetAddress']['address']['municipality'];
            }

            if (isset($this->formValues['streetAddress']) && isset($this->formValues['streetAddress']['street'])) {
                $street = $this->formValues['streetAddress']['street'];
            }
        } else {
            $province = (empty($this->formValues['streetAddress']['address']['province']) ? $this->ic->getMunicipality()?->getProvince()?->getId() : (int) $this->formValues['streetAddress']['address']['province']);
            $municipality = (empty($this->formValues['streetAddress']['address']['municipality']) ? $this->ic->getMunicipality()?->getId() : (int) $this->formValues['streetAddress']['address']['municipality']);
            $street = (empty($this->formValues['streetAddress']['street']) ? $this->ic->getStreet() : $this->formValues['streetAddress']['street']);
        }

        return $this->createForm(IndividualClientType::class, $this->ic, [
            'street' => $street ?? '',
            'province' => $province ?? 0,
            'municipality' => $municipality ?? 0,
            'live_form' => ('on(change)|*' === $this->getDataModelValue()),
        ]);
    }

    #[LiveAction]
    public function save(IndividualClientRepository $individualClientRepository, RepresentativeRepository $representativeRepository): ?Response
    {
        $this->preValue();

        $successMsg = (is_null($this->ic?->getId())) ? 'Se ha agregado el cliente.' : 'Se ha modificado el cliente.'; // TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var IndividualClient $ic */
            $ic = $this->getForm()->getData();

            $person = $this->createPerson($ic);
            $ic->setPerson($person);

            $representative = $representativeRepository->find((int) $this->formValues['representative']);
            $ic->setRepresentative($representative);

            $ic->setStreet($this->formValues['streetAddress']['street']);

            $municipality = $this->municipalityRepository->find((int) $this->formValues['streetAddress']['address']['municipality']);
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
