<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\EnterpriseClient;
use App\Form\EnterpriseClientType;
use App\Repository\CorporateEntityRepository;
use App\Repository\EnterpriseClientRepository;
use App\Repository\MunicipalityRepository;
use App\Repository\PersonRepository;
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

#[AsLiveComponent(template: 'component/live/enterprise_client_form.html.twig')]
final class EnterpriseClientForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?EnterpriseClient $ec = null;

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
    public int $corporateEntity = 0;

    #[LiveProp(writable: true)]
    public int $representative = 0;

    public function __construct(
        protected readonly ProvinceRepository $provinceRepository,
        protected readonly MunicipalityRepository $municipalityRepository,
        protected readonly CorporateEntityRepository $corporateEntityRepository,
        //        protected readonly PersonRepository $personRepository
    ) {
    }

    public function mount(?EnterpriseClient $ec = null): void
    {
        $this->ec = $ec;
        if (is_null($this->ec)) {
            $this->ec = new EnterpriseClient();
        }
    }

    public function preValue(): void
    {
        /** @var array<string, array<string, array<string, mixed>>> $formValues */
        $formValues = $this->formValues;

        if (0 !== $this->corporateEntity) {
            $formValues['corporateEntity'] = (string) $this->corporateEntity;
            $this->corporateEntity = 0;
            $this->formValues = $formValues;
        }

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
                                $formValues['streetAddress']['address']['municipality'] = ($prov->getMunicipalities()->count() && $prov->getMunicipalities()->first())
                                    ? (string) $prov->getMunicipalities()->first()->getId()
                                    : '';
                            }
                        }
                    } else {
                        $prov = $this->provinceRepository->find($formValues['streetAddress']['address']['province']);
                        if (!is_null($prov)) {
                            if ($prov->getMunicipalities()->count() && $prov->getMunicipalities()->first()) {
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
     * @return FormInterface<EnterpriseClient>
     */
    protected function instantiateForm(): FormInterface
    {
        $this->preValue();
        /** @var array<string, array<string, array<string, mixed>>> $formValues */
        $formValues = $this->formValues;

        if (!$this->ec?->getId()) {
            if (isset($formValues['streetAddress']['address'])) {
                /** @var int $province */
                $province = $formValues['streetAddress']['address']['province'];
                /** @var int $municipality */
                $municipality = $formValues['streetAddress']['address']['municipality'];
            }
            if (isset($formValues['streetAddress']['street'])) {
                $street = $formValues['streetAddress']['street'];
            }
        } else {
            $mun = $this->ec->getMunicipality();
            /** @var int $province */
            $province = (empty($formValues['streetAddress']['address']['province']) ? $mun?->getProvince()?->getId() : $formValues['streetAddress']['address']['province']);
            /** @var int $municipality */
            $municipality = (empty($formValues['streetAddress']['address']['municipality']) ? $mun?->getId() : $formValues['streetAddress']['address']['municipality']);
            /** @var string $street */
            $street = (empty($formValues['streetAddress']['street']) ? $this->ec->getStreet() : $formValues['streetAddress']['street']);
        }

        return $this->createForm(EnterpriseClientType::class, $this->ec, [
            'street' => $street ?? '',
            'province' => $province ?? 0,
            'municipality' => $municipality ?? 0,
            'live_form' => ('on(change)|*' === $this->getDataModelValue()),
        ]);
    }

    #[LiveAction]
    public function save(EnterpriseClientRepository $enterpriseClientRepository, RepresentativeRepository $representativeRepository): ?Response
    {
        $this->preValue();

        /** @var array<string, array<string, array<string, mixed>>> $formValues */
        $formValues = $this->formValues;

        $successMsg = (is_null($this->ec?->getId())) ? 'Se ha agregado el cliente.' : 'Se ha modificado el cliente.'; // TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var EnterpriseClient $ec */
            $ec = $this->getForm()->getData();
            $msgEntityPlus = ($ec->getId()) ? 'Se a modificado el cliente empresarial.' : 'Se ha seleccionado el nuevo cliente empresarial.';

            /** @var string $street */
            $street = $formValues['streetAddress']['street'];
            $ec->setStreet($street);

            $municipality = $this->municipalityRepository->find($formValues['streetAddress']['address']['municipality']);
            $ec->setMunicipality($municipality);

            $corporateEntity = $this->corporateEntityRepository->find($formValues['corporateEntity']);
            $ec->setCorporateEntity($corporateEntity);

            $representative = $representativeRepository->find($formValues['representative']);
            $ec->setRepresentative($representative);

            $enterpriseClientRepository->save($ec, true);

            $this->ec = new EnterpriseClient();
            if (!is_null($this->modal)) {
                $this->modalManage($ec, $msgEntityPlus, [
                    'enterpriseClient' => $ec->getId(),
                ], 'text-bg-success');

                return null;
            }
            if ($this->ajax) {
                $this->ajaxManage($ec, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('app_enterprise_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): string
    {
        return 'norender|*';
    }
}
