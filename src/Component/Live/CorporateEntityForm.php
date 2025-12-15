<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\CorporateEntity;
use App\Form\CorporateEntityType;
use App\Repository\CorporateEntityRepository;
use App\Repository\MunicipalityRepository;
use App\Repository\OrganismRepository;
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

#[AsLiveComponent(template: 'component/live/corporate_entity_form.html.twig')]
final class CorporateEntityForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?CorporateEntity $ce = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp(writable: true)]
    public ?int $organism = 0;

    #[LiveProp(writable: true)]
    public int $province = 0;

    #[LiveProp(writable: true)]
    public int $municipality = 0;

    public function __construct(protected readonly ProvinceRepository $provinceRepository, protected readonly MunicipalityRepository $municipalityRepository)
    {
    }

    public function mount(?CorporateEntity $ce = null): void
    {
        $this->ce = $ce;
        if (is_null($this->ce)) {
            $this->ce = new CorporateEntity();
        }
    }

    public function preValue(): void
    {
        /** @var array<string, array<string, mixed>> $formValues */
        $formValues = $this->formValues;

        if (0 !== $this->organism) {
            $formValues['organism'] = (string) $this->organism;
            $this->organism = 0;
            $this->formValues = $formValues;
        }

        if (0 !== $this->province) {
            /** @var array<string, mixed> $address */
            $address = $formValues['address'] ?? [];
            $address['province'] = (string) $this->province;
            $formValues['address'] = $address;
            $this->province = 0;
            $this->formValues = $formValues;
        }

        if (0 !== $this->municipality) {
            /** @var array<string, mixed> $address */
            $address = $formValues['address'] ?? [];
            $address['municipality'] = (string) $this->municipality;
            $formValues['address'] = $address;
            $this->municipality = 0;
            $this->formValues = $formValues;
        } else {
            /** @var array<string, array<string, mixed>> $formValues */
            $formValues = $this->formValues;
            if (isset($formValues['address'])) {
                if (isset($formValues['address']['province'])) {
                    if (true === (bool) $formValues['address']['municipality']) {
                        $mun = $this->municipalityRepository->find($formValues['address']['municipality']);
                        if ((string) $mun?->getProvince()?->getId() !== $formValues['address']['province']) {
                            $prov = $this->provinceRepository->find($formValues['address']['province']);
                            if (!is_null($prov)) {
                                $formValues['address']['municipality'] = ($prov->getMunicipalities()->count() > 0 && false !== $prov->getMunicipalities()->first())
                                    ? (string) $prov->getMunicipalities()->first()->getId()
                                    : '';
                            }
                        }
                    } else {
                        $prov = $this->provinceRepository->find($formValues['address']['province']);
                        if (!is_null($prov)) {
                            if ($prov->getMunicipalities()->count() > 0 && false !== $prov->getMunicipalities()->first()) {
                                $formValues['address']['municipality'] = (string) $prov->getMunicipalities()->first()->getId();
                            }
                        }
                    }
                }
            }

            $this->formValues = $formValues;
        }
    }

    /**
     * @return FormInterface<CorporateEntity>
     */
    protected function instantiateForm(): FormInterface
    {
        $this->preValue();

        /** @var array<string, array<string, mixed>> $formValues */
        $formValues = $this->formValues;

        if (null === $this->ce?->getId()) {
            if (isset($formValues['address'])) {
                /** @var int $province */
                $province = $formValues['address']['province'];
                /** @var int $municipality */
                $municipality = $formValues['address']['municipality'];
            }
        } else {
            $municipality = $this->ce->getMunicipality();
            /** @var int $province */
            $province = (false === (bool) $formValues['address']['province'] ? $municipality?->getProvince()?->getId() : $formValues['address']['province']);
            $municipality = (false === (bool) $formValues['address']['municipality'] ? $municipality?->getId() : $formValues['address']['municipality']);
        }

        return $this->createForm(CorporateEntityType::class, $this->ce, [
            'province' => $province ?? 0,
            'municipality' => $municipality ?? 0,
            'live_form' => ('on(change)|*' === $this->getDataModelValue()),
            'modal' => $this->modal,
        ]);
    }

    #[LiveAction]
    public function save(CorporateEntityRepository $corporateEntityRepository, OrganismRepository $organismRepository): ?Response
    {
        $this->preValue();

        /** @var array<string, array<string, mixed>> $formValues */
        $formValues = $this->formValues;

        $successMsg = (is_null($this->ce?->getId())) ? 'Se ha agregado la entidad corporativa.' : 'Se ha modificado la entidad corporativa.'; // TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var CorporateEntity $ce */
            $ce = $this->getForm()->getData();

            $organism = $organismRepository->find($formValues['organism']);
            $ce->setOrganism($organism);

            $municipality = $this->municipalityRepository->find($formValues['address']['municipality']);
            $ce->setMunicipality($municipality);

            $corporateEntityRepository->save($ce, true);

            $this->ce = new CorporateEntity();
            if (!is_null($this->modal)) {
                $this->modalManage($ce, 'Se ha seleccionado la nueva entidad corporativa agregada.', [
                    'corporateEntity' => $ce->getId(),
                ]);

                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($ce, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('app_corporate_entity_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): string
    {
        return 'norender|*';
    }
}
