<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Component\Twig\Modal\Modal;
use App\Entity\CorporateEntity;
use App\Entity\Investment;
use App\Form\CorporateEntityType;
use App\Form\InvestmentType;
use App\Repository\ConstructorRepository;
use App\Repository\CorporateEntityRepository;
use App\Repository\InvestmentRepository;
use App\Repository\LocationZoneRepository;
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

#[AsLiveComponent(template: 'component/live/investment_form.html.twig')]
final class InvestmentForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

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
        protected readonly MunicipalityRepository $municipalityRepository
    )
    {

    }

    public function mount(?Investment $inv = null): void
    {
        $this->inv = $inv;
        if (is_null($this->inv)) {
            $this->inv = new Investment();
        }
    }

    /**
     * @return void
     */
    public function preValue(): void
    {
        if ($this->locationZone !== 0) {
            $this->formValues['location_zone'] = (string)$this->locationZone;
            $this->locationZone = 0;
        }

        if ($this->street !== '') {
            $this->formValues['streetAddress']['street'] = (string)$this->street;
            $this->street = '';
        }

        if ($this->province !== 0) {
            $this->formValues['streetAddress']['address']['province'] = (string)$this->province;
            $this->province = 0;
        }

        if ($this->municipality !== 0) {
            $this->formValues['streetAddress']['address']['municipality'] = (string)$this->municipality;
            $this->municipality = 0;
        } else {
            if (isset($this->formValues['streetAddress']) && isset($this->formValues['streetAddress']['address'])) {
                if (isset($this->formValues['streetAddress']['address']['province'])) {
                    if ($this->formValues['streetAddress']['address']['municipality']) {
                        $mun = $this->municipalityRepository->find((int)$this->formValues['streetAddress']['address']['municipality']);
                        if ((string)$mun->getProvince()->getId() !== $this->formValues['streetAddress']['address']['province']) {
                            $prov = $this->provinceRepository->find((int)$this->formValues['streetAddress']['address']['province']);
                            if (!is_null($prov)) {
                                $this->formValues['streetAddress']['address']['municipality'] = ($prov->getMunicipalities()->count())
                                    ? (string)$prov->getMunicipalities()->first()->getId()
                                    : '';
                            }
                        }
                    } else {
                        $prov = $this->provinceRepository->find((int)$this->formValues['streetAddress']['address']['province']);
                        if (!is_null($prov)) {
                            if ($prov->getMunicipalities()->count()) {
                                $this->formValues['streetAddress']['address']['municipality'] = (string)$prov->getMunicipalities()->first()->getId();
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

        if (!$this->inv->getId()) {
            if (isset($this->formValues['streetAddress']) && isset($this->formValues['streetAddress']['address'])) {
                $province = (int)$this->formValues['streetAddress']['address']['province'];
                $municipality = (int)$this->formValues['streetAddress']['address']['municipality'];
            }
            if (isset($this->formValues['streetAddress']) && isset($this->formValues['streetAddress']['street'])) {
                $street = $this->formValues['streetAddress']['street'];
            }
        } else {
            $province = (empty($this->formValues['streetAddress']['address']['province']) ? $this->inv->getMunicipality()->getProvince()->getId() : (int)$this->formValues['streetAddress']['address']['province']);
            $municipality = (empty($this->formValues['streetAddress']['address']['municipality']) ? $this->inv->getMunicipality()->getId() : (int)$this->formValues['streetAddress']['address']['municipality']);
            $street = (empty($this->formValues['streetAddress']['street']) ? $this->inv->getStreet() : $this->formValues['streetAddress']['street']);
        }

        return $this->createForm(InvestmentType::class, $this->inv, [
            'street' => $street ?? '',
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
    public function save(InvestmentRepository $investmentRepository, LocationZoneRepository $locationZoneRepository): ?Response
    {
        $this->preValue();

        $successMsg = (is_null($this->inv->getId())) ? 'Se ha agregado la inversión.' : 'Se ha modificado la inversión.';//TODO: personalizar los mensajes

        $this->submitForm();
        if ($this->isSubmitAndValid()) {
            /** @var Investment $inv */
            $inv = $this->getForm()->getData();

            $inv->setStreet($this->formValues['streetAddress']['street']);

            $locationZone = $locationZoneRepository->find((int)$this->formValues['locationZone']);
            $inv->setLocationZone($locationZone);

            $municipality = $this->municipalityRepository->find((int)$this->formValues['streetAddress']['address']['municipality']);
            $inv->setMunicipality($municipality);

            $investmentRepository->save($inv, true);

            $this->inv = new Investment();
            if (!is_null($this->modal)) {
                $this->modalManage($inv, 'Seleccione la nueva inversión agregada.', [
                    'investment' => $inv->getId()
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

    private function getDataModelValue(): ?string
    {
        return 'norender|*';
    }

}
