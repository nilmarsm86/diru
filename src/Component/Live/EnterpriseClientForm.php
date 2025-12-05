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
        protected readonly ProvinceRepository        $provinceRepository,
        protected readonly MunicipalityRepository    $municipalityRepository,
        protected readonly CorporateEntityRepository $corporateEntityRepository,
        protected readonly PersonRepository $personRepository
    )
    {

    }

    public function mount(?EnterpriseClient $ec = null): void
    {
        $this->ec = $ec;
        if (is_null($this->ec)) {
            $this->ec = new EnterpriseClient();
        }
    }

    /**
     * @return void
     */
    public function preValue(): void
    {
        if ($this->corporateEntity !== 0) {
            $this->formValues['corporateEntity'] = (string)$this->corporateEntity;
            $this->corporateEntity = 0;
        }

        if ($this->representative !== 0) {
            $this->formValues['representative'] = (string)$this->representative;
            $this->representative = 0;
        }

        if ($this->street !== '') {
            $this->formValues['streetAddress']['street'] = (string)$this->street;
            $this->street = '';
        }

        if ($this->province !== 0) {
            $this->formValues['streetAddress']['address']['province'] = (string)$this->province;
            $this->province = 0;
        }

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

    protected function instantiateForm(): FormInterface
    {
        $this->preValue();

        if (!$this->ec->getId()) {
            if (isset($this->formValues['streetAddress']) && isset($this->formValues['streetAddress']['address'])) {
                $province = (int)$this->formValues['streetAddress']['address']['province'];
                $municipality = (int)$this->formValues['streetAddress']['address']['municipality'];
            }
            if (isset($this->formValues['streetAddress']) && isset($this->formValues['streetAddress']['street'])) {
                $street = $this->formValues['streetAddress']['street'];
            }
        } else {
            $province = (empty($this->formValues['streetAddress']['address']['province']) ? $this->ec->getMunicipality()->getProvince()->getId() : (int)$this->formValues['streetAddress']['address']['province']);
            $municipality = (empty($this->formValues['streetAddress']['address']['municipality']) ? $this->ec->getMunicipality()->getId() : (int)$this->formValues['streetAddress']['address']['municipality']);
            $street = (empty($this->formValues['streetAddress']['street']) ? $this->ec->getStreet() : $this->formValues['streetAddress']['street']);
        }

        return $this->createForm(EnterpriseClientType::class, $this->ec, [
            'street' => $street ?? '',
            'province' => $province ?? 0,
            'municipality' => $municipality ?? 0,
            'live_form' => ($this->getDataModelValue() === 'on(change)|*')
        ]);
    }

    /**
     * @throws Exception
     */
    #[LiveAction]
    public function save(EnterpriseClientRepository $enterpriseClientRepository): ?Response
    {
        $this->preValue();

        $successMsg = (is_null($this->ec->getId())) ? 'Se ha agregado el cliente.' : 'Se ha modificado el cliente.';//TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var EnterpriseClient $ec */
            $ec = $this->getForm()->getData();
            $msgEntityPlus = ($ec->getId()) ? 'Se a modificado el cliente empresarial.' : 'Se ha seleccionado el nuevo cliente empresarial.';

            $ec->setStreet($this->formValues['streetAddress']['street']);

            $municipality = $this->municipalityRepository->find((int)$this->formValues['streetAddress']['address']['municipality']);
            $ec->setMunicipality($municipality);

            $corporateEntity = $this->corporateEntityRepository->find((int)$this->formValues['corporateEntity']);
            $ec->setCorporateEntity($corporateEntity);

            $representative = $this->personRepository->find((int)$this->formValues['representative']);
            $ec->setRepresentative($representative);

            $enterpriseClientRepository->save($ec, true);

            $this->ec = new EnterpriseClient();
            if (!is_null($this->modal)) {
                $this->modalManage($ec, $msgEntityPlus, [
                    'enterpriseClient' => $ec->getId()
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
