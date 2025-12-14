<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Building;
use App\Entity\Project;
use App\Form\BuildingType;
use App\Repository\BuildingRepository;
use App\Repository\ConstructorRepository;
use App\Repository\DraftsmanRepository;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent(template: 'component/live/building_form.html.twig')]
final class BuildingForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;
    use LiveCollectionTrait;

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

    #[LiveProp(writable: true)]
    public ?float $urbanizationEstimateTotalPrice = 0;

    #[LiveProp(writable: true)]
    public ?float $ptpEstimateTotalPrice = 0;

    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    public function mount(?Building $bui = null): void
    {
        $this->bui = (is_null($bui)) ? new Building() : $bui;
        //        $this->project = $project;
        if (!is_null($this->bui->getId())) {
            $this->urbanizationEstimateTotalPrice = $this->bui->getUrbanizationEstimateTotalPrice();
            $this->ptpEstimateTotalPrice = $this->bui->getProjectTechnicalPreparationEstimateTotalPrice();
        }
    }

    public function preValue(): void
    {
        if (0 !== $this->constructor) {
            $this->formValues['constructor'] = (string) $this->constructor;
            $this->constructor = 0;
        }

        if (0 !== $this->project) {
            //            $this->formValues['project'] = (string)$this->project;
            $project = $this->projectRepository->find((int) $this->project);
            $this->bui?->setProject($project);
            //            $this->project = 0;
        }

        if (0.0 !== $this->urbanizationEstimateTotalPrice) {
            $this->formValues['estimatedValueUrbanization'] = (float) $this->urbanizationEstimateTotalPrice / 100;
        }

        if (0.0 !== $this->ptpEstimateTotalPrice) {
            $this->formValues['projectPriceTechnicalPreparation'] = (float) $this->ptpEstimateTotalPrice / 100;
        }
    }

    /**
     * @return FormInterface<Building>
     */
    protected function instantiateForm(): FormInterface
    {
        $this->preValue();

        return $this->createForm(BuildingType::class, $this->bui, [
            'screen' => 'building',
            'urbanizationEstimate' => $this->urbanizationEstimateTotalPrice,
            'ptpEstimate' => $this->ptpEstimateTotalPrice,
        ]);
    }

    #[LiveAction]
    public function save(
        BuildingRepository $buildingRepository,
        ConstructorRepository $constructorRepository,
        ProjectRepository $projectRepository,
        DraftsmanRepository $draftsmanRepository,
    ): ?Response {
        $this->preValue();

        $successMsg = (is_null($this->bui?->getId())) ? 'Se ha agregado la obra.' : 'Se ha modificado la obra.';

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var Building $building */
            $building = $this->getForm()->getData();

            if (false !== (bool)$this->formValues['constructor']) {
                $constructor = $constructorRepository->find($this->formValues['constructor']);
                if (null !== $constructor) {
                    $building->addConstructor($constructor);
                }
            }

            if (0 !== $this->project) {
                $project = $projectRepository->find((int) $this->project);
                assert($project instanceof Project);
                $building->setProject($project);
            }

            if (false !== (bool)$this->formValues['draftsman']) {
                $draftsman = $draftsmanRepository->find($this->formValues['draftsman']);
                if (null !== $draftsman) {
                    $building->addDraftsman($draftsman);
                }
            }

            $buildingRepository->save($building, true);

            $this->bui = new Building();
            if (!is_null($this->modal)) {
                $this->modalManage($building, 'Se ha seleccionado la nueva obra agregada.', [
                    'building' => $building->getId(),
                ]);

                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($building, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('app_building_edit', ['id' => $building->getId()], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): string
    {
        return 'norender|*';
    }
}
