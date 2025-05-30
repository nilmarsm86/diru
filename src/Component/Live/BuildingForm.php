<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Component\Twig\Modal\Modal;
use App\Entity\Building;
use App\Entity\Constructor;
use App\Entity\Province;
use App\Form\BuildingType;
use App\Form\ConstructorType;
use App\Form\ProvinceType;
use App\Repository\BuildingRepository;
use App\Repository\ConstructorRepository;
use App\Repository\InvestmentRepository;
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

#[AsLiveComponent(template: 'component/live/building_form.html.twig')]
final class BuildingForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?Building $bui = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp(writable: true)]
    public ?int $constructor = 0;

    #[LiveProp(writable: true)]
    public ?int $investment = 0;

    public function mount(?Building $bui = null): void
    {
        $this->bui = (is_null($bui)) ? new Building() : $bui;
    }

    /**
     * @return void
     */
    public function preValue(): void
    {
        if ($this->constructor !== 0) {
            $this->formValues['constructor'] = (string)$this->constructor;
            $this->constructor = 0;
        }

        if ($this->investment !== 0) {
            $this->formValues['investment'] = (string)$this->investment;
            $this->investment = 0;
        }
    }

    protected function instantiateForm(): FormInterface
    {
        $this->preValue();
        return $this->createForm(BuildingType::class, $this->bui);
    }

    /**
     * @throws Exception
     */
    #[LiveAction]
    public function save(BuildingRepository $buildingRepository, ConstructorRepository $constructorRepository, InvestmentRepository $investmentRepository): ?Response
    {
        $this->preValue();
        $successMsg = (is_null($this->bui->getId())) ? 'Se ha agregado la obra.' : 'Se ha modificado la obra.';

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var Building $building */
            $building = $this->getForm()->getData();

            $constructor = $constructorRepository->find((int)$this->formValues['constructor']);
            $building->setConstructor($constructor);

            $investment = $investmentRepository->find((int)$this->formValues['investment']);
            $building->setInvestment($investment);

            $buildingRepository->save($building, true);

            $this->bui = new Building();
            if (!is_null($this->modal)) {
                $this->modalManage($building, 'Seleccione la nueva obra agregada.', [
                    'constructor' => $building->getId()
                ]);
                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($building, $successMsg);
                return null;
            }

            $this->addFlash('success', $successMsg);
            return $this->redirectToRoute('app_building_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): ?string
    {
        return 'norender|*';
    }

}
