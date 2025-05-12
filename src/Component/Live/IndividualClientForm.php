<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\IndividualClient;
use App\Form\IndividualClientType;
use App\Repository\IndividualClientRepository;
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
    public int $person = 0;

    public function __construct(
        protected readonly ProvinceRepository $provinceRepository,
        protected readonly MunicipalityRepository $municipalityRepository,
        protected readonly PersonRepository $personRepository
    )
    {

    }

    public function mount(?IndividualClient $ic = null): void
    {
        $this->ic = $ic;
        if (is_null($this->ic)) {
            $this->ic = new IndividualClient();
        }
    }

    /**
     * @param string $successMsg
     * @return void
     */
    public function ajaxManage(string $successMsg): void
    {
        $template = $this->renderView("partials/_form_success.html.twig", [
            'id' => 'new_' . $this->getClassName($this->ic::class) . '_' . $this->ic->getId(),
            'type' => 'text-bg-success',
            'message' => $successMsg
        ]);

        $this->ic = new IndividualClient();
        $this->emitSuccess([
            'response' => $template
        ]);
    }

    /**
     * @return void
     */
    public function preValue(): void
    {
        if ($this->person !== 0) {
            $this->formValues['person'] = (string)$this->person;
            $this->person = 0;
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

        if (!$this->ic->getId()) {
            if (isset($this->formValues['streetAddress']) && isset($this->formValues['streetAddress']['address'])) {
                $province = (int)$this->formValues['streetAddress']['address']['province'];
                $municipality = (int)$this->formValues['streetAddress']['address']['municipality'];
            }

            if (isset($this->formValues['streetAddress']) && isset($this->formValues['streetAddress']['street'])) {
//            if (isset($this->formValues['streetAddress']['street'])) {
                $street = $this->formValues['streetAddress']['street'];
            }
        } else {
            $province = (empty($this->formValues['streetAddress']['address']['province']) ? $this->ic->getMunicipality()->getProvince()->getId() : (int)$this->formValues['streetAddress']['address']['province']);
            $municipality = (empty($this->formValues['streetAddress']['address']['municipality']) ? $this->ic->getMunicipality()->getId() : (int)$this->formValues['streetAddress']['address']['municipality']);
            $street = (empty($this->formValues['streetAddress']['street']) ? $this->ic->getStreet() : $this->formValues['streetAddress']['street']);
        }

        return $this->createForm(IndividualClientType::class, $this->ic, [
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
    public function save(IndividualClientRepository $individualClientRepository, MunicipalityRepository $municipalityRepository, PersonRepository $personRepository): ?Response
    {
        $this->preValue();

        $successMsg = (is_null($this->ic->getId())) ? 'Se ha agregado el cliente.' : 'Se ha modificado el cliente.';//TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var IndividualClient $ic */
            $ic = $this->getForm()->getData();

            $person = $personRepository->find((int)$this->formValues['person']);
            $ic->setPerson($person);

            $ic->setStreet($this->formValues['streetAddress']['street']);

            $municipality = $municipalityRepository->find((int)$this->formValues['streetAddress']['address']['municipality']);
            $ic->setMunicipality($municipality);

            $individualClientRepository->save($ic, true);

//            if ($this->modal) {
//                $this->modalManage($ce);
//                return null;
//            }

            if ($this->ajax) {
                $this->ajaxManage($successMsg);
                return null;
            }

            $this->addFlash('success', $successMsg);
            return $this->redirectToRoute('app_individual_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): ?string
    {
        return 'norender|*';
    }

}
