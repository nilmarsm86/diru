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
use App\Repository\DraftsmanRepository;
use App\Repository\InvestmentRepository;
use App\Repository\ProjectRepository;
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
    public ?int $project = 0;

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

        if ($this->project !== 0) {
            $this->formValues['project'] = (string)$this->project;
            $this->project = 0;
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
    public function save(
        BuildingRepository $buildingRepository,
        ConstructorRepository $constructorRepository,
        ProjectRepository $projectRepository,
        DraftsmanRepository  $draftsmanRepository
    ): ?Response
    {
        $this->preValue();
        $successMsg = (is_null($this->bui->getId())) ? 'Se ha agregado la obra.' : 'Se ha modificado la obra.';

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var Building $building */
            $building = $this->getForm()->getData();

            if (!empty($this->formValues['constructor'])) {
                $constructor = $constructorRepository->find((int)$this->formValues['constructor']);
                $building->addConstructor($constructor);
            }

//            $project = $projectRepository->find((int)$this->formValues['project']);
//            $building->setProject($project);
            if (!empty($this->formValues['draftsman'])) {
                $draftsman = $draftsmanRepository->find($this->formValues['draftsman']);
                $building->addDraftsman($draftsman);
            }

            $buildingRepository->save($building, true);

            $this->bui = new Building();
            if (!is_null($this->modal)) {
                $this->modalManage($building, 'Se ha seleccionado la nueva obra agregada.', [
                    'building' => $building->getId()
                ]);
                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($building, $successMsg);
                return null;
            }

            $this->addFlash('success', $successMsg);
            return $this->redirectToRoute('app_building_index', ['filter' => $building->getProject()->getName()], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): ?string
    {
        return 'norender|*';
    }

}
