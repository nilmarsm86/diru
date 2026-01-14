<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Constructor;
use App\Form\ConstructorType;
use App\Repository\ConstructorRepository;
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

#[AsLiveComponent(template: 'component/live/constructor_form.html.twig')]
final class ConstructorForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?Constructor $cons = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public ?string $screen = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp(writable: true)]
    public int $province = 0;

    #[LiveProp(writable: true)]
    public int $municipality = 0;

    #[LiveProp(writable: true)]
    public ?string $street = '';

    public function __construct(
        protected readonly ProvinceRepository $provinceRepository,
        protected readonly MunicipalityRepository $municipalityRepository,
    ) {
    }

    public function mount(?Constructor $cons = null, string $screen = 'project'): void
    {
        $this->cons = (is_null($cons)) ? new Constructor() : $cons;
        $this->screen = $screen;
    }

    public function preValue(): void
    {
        /** @var array<string, array<string, mixed>> $formValues */
        $formValues = $this->formValues;

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
     * @return FormInterface<Constructor>
     */
    protected function instantiateForm(): FormInterface
    {
        $this->preValue();

        /** @var array<string, array<string, array<string, mixed>>> $formValues */
        $formValues = $this->formValues;
        $province = 0;
        $municipality = 0;

        if (null === $this->cons?->getId()) {
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
            $mun = $this->cons->getMunicipality();
            /** @var int $province */
            $province = $formValues['streetAddress']['address']['province'] ?? $mun?->getProvince()?->getId();
            /** @var int $municipality */
            $municipality = $formValues['streetAddress']['address']['municipality'] ?? $mun?->getId();
            /** @var string $street */
            $street = $formValues['streetAddress']['street'] ?? $this->cons->getStreet();
        }

        return $this->createForm(ConstructorType::class, $this->cons, [
            'street' => $street ?? '',
            'province' => (int) $province,
            'municipality' => (int) $municipality,
            'live_form' => ('on(change)|*' === $this->getDataModelValue()),
        ]);
    }

    #[LiveAction]
    public function save(ConstructorRepository $constructorRepository): ?Response
    {
        $this->preValue();

        /** @var array<string, array<string, mixed>> $formValues */
        $formValues = $this->formValues;

        $successMsg = (is_null($this->cons?->getId())) ? 'Se ha agregado la constructora.' : 'Se ha modificado la constructora.';

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var Constructor $constructor */
            $constructor = $this->getForm()->getData();

            /** @var string $street */
            $street = $formValues['streetAddress']['street'];
            $constructor->setStreet($street);

            /** @var array<string, array<string, array<string, mixed>>> $formValues */
            $formValues = $this->formValues;
            $municipality = $this->municipalityRepository->find($formValues['streetAddress']['address']['municipality']);
            $constructor->setMunicipality($municipality);

            $constructorRepository->save($constructor, true);

            $this->cons = new Constructor();
            if (!is_null($this->modal)) {
                if ('building' === $this->screen) {
                    $this->modalManage($constructor, 'Se ha seleccionado la nueva constructora agregada.', [
                        'constructor' => $constructor->getId(),
                    ]);
                }

                if ('project' === $this->screen) {
                    $this->modalManage($constructor, 'Se ha agregado una nueva constructora, seleccionela.', [
                        'constructor' => $constructor->getId(),
                    ], 'text-bg-success');
                }

                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($constructor, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('app_constructor_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): string
    {
        return 'norender|*';
    }
}
