<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\CorporateEntity;
use App\Form\CorporateEntityType;
use App\Repository\CorporateEntityRepository;
use App\Repository\MunicipalityRepository;
use App\Repository\OrganismRepository;
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

    /**
     * @return void
     */
    public function preValue(): void
    {
        if ($this->organism !== 0) {
            $this->formValues['organism'] = (string)$this->organism;
            $this->organism = 0;
        }

        if ($this->province !== 0) {
            $this->formValues['address']['province'] = (string)$this->province;
            $this->province = 0;
        }

        if ($this->municipality !== 0) {
            $this->formValues['address']['municipality'] = (string)$this->municipality;
            $this->municipality = 0;
        } else {
            if (isset($this->formValues['address'])) {
                if (isset($this->formValues['address']['province'])) {
                    if ($this->formValues['address']['municipality']) {
                        $mun = $this->municipalityRepository->find((int)$this->formValues['address']['municipality']);
                        if ((string)$mun->getProvince()->getId() !== $this->formValues['address']['province']) {
                            $prov = $this->provinceRepository->find((int)$this->formValues['address']['province']);
                            if (!is_null($prov)) {
                                $this->formValues['address']['municipality'] = ($prov->getMunicipalities()->count())
                                    ? (string)$prov->getMunicipalities()->first()->getId()
                                    : '';
                            }
                        }
                    } else {
                        $prov = $this->provinceRepository->find((int)$this->formValues['address']['province']);
                        if (!is_null($prov)) {
                            if ($prov->getMunicipalities()->count()) {
                                $this->formValues['address']['municipality'] = (string)$prov->getMunicipalities()->first()->getId();
                            }
                        }
                    }
                }
            }
        }
    }

    protected function instantiateForm(): FormInterface
    {
        $this->preValue();

        if (!$this->ce->getId()) {
            if (isset($this->formValues['address'])) {
                $province = (int)$this->formValues['address']['province'];
                $municipality = (int)$this->formValues['address']['municipality'];
            }
        } else {
            $province = (empty($this->formValues['address']['province']) ? $this->ce->getMunicipality()->getProvince()->getId() : (int)$this->formValues['address']['province']);
            $municipality = (empty($this->formValues['address']['municipality']) ? $this->ce->getMunicipality()->getId() : (int)$this->formValues['address']['municipality']);
        }

        return $this->createForm(CorporateEntityType::class, $this->ce, [
            'province' => $province ?? 0,
            'municipality' => $municipality ?? 0,
            'live_form' => ($this->getDataModelValue() === 'on(change)|*'),
            'modal' => $this->modal
        ]);
    }

    /**
     * @throws Exception
     */
    #[LiveAction]
    public function save(CorporateEntityRepository $corporateEntityRepository, OrganismRepository $organismRepository): ?Response
    {
        $this->preValue();

        $successMsg = (is_null($this->ce->getId())) ? 'Se ha agregado la entidad corporativa.' : 'Se ha modificado la entidad corporativa.';//TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var CorporateEntity $ce */
            $ce = $this->getForm()->getData();

            $organism = $organismRepository->find((int)$this->formValues['organism']);
            $ce->setOrganism($organism);

            $municipality = $this->municipalityRepository->find((int)$this->formValues['address']['municipality']);
            $ce->setMunicipality($municipality);

            $corporateEntityRepository->save($ce, true);

            $this->ce = new CorporateEntity();
            if (!is_null($this->modal)) {
                $this->modalManage($ce, 'Se ha seleccionado la nueva entidad corporativa agregada.', [
                    'corporateEntity' => $ce->getId()
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
