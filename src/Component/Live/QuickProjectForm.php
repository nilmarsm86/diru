<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Currency;
use App\Entity\Project;
use App\Form\QuickProjectType;
use App\Repository\ClientRepository;
use App\Repository\CurrencyRepository;
use App\Repository\DraftsmanRepository;
use App\Repository\MunicipalityRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'component/live/quick_project_form.html.twig')]
final class QuickProjectForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?Project $pro = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp(writable: true)]
    public ?int $investment = 0;

    #[LiveProp(writable: true)]
    public ?int $client = 0;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {

    }

    public function mount(?Project $pro = null): void
    {
        $this->pro = (is_null($pro)) ? new Project() : $pro;
        $currency = $this->entityManager->getRepository(Currency::class)->findOneBy(['code' => 'CUP']);
        $this->pro->setCurrency($currency);
    }

    /**
     * @return void
     */
    public function preValue(): void
    {
        if ($this->investment !== 0) {
            $this->formValues['investment'] = (string)$this->investment;
            $this->investment = 0;
        }

        if ($this->client !== 0) {
            $this->formValues['client'] = (string)$this->client;
            $this->client = 0;
        }
    }

    protected function instantiateForm(): FormInterface
    {
        $this->preValue();
        $currency = $this->entityManager->getRepository(Currency::class)->findOneBy(['code' => 'CUP']);
        $this->pro->setCurrency($currency);
        return $this->createForm(QuickProjectType::class, $this->pro);
    }

    /**
     * @throws Exception
     */
    #[LiveAction]
    public function save(
        ProjectRepository $projectRepository,
        ClientRepository $clientRepository,
        MunicipalityRepository $municipalityRepository,
        DraftsmanRepository $draftsmanRepository,
        Security             $security,
        CurrencyRepository $currencyRepository
    ): ?Response
    {
        $this->preValue();
        $successMsg = (is_null($this->pro->getId())) ? 'Se ha agregado el proyecto.' : 'Se ha modificado el proyecto.';

        $this->submitForm();
//        $project = $this->getForm()->getData();
//        $project->setCurrency($currencyRepository->findOneBy(['code'=>'CUP']));
        if ($this->isSubmitAndValid()) {
            /** @var Project $project */
            $project = $this->getForm()->getData();

            if($this->formValues['individualClient']){
                $this->formValues['client'] = (int)$this->formValues['individualClient'];
            }

            if($this->formValues['enterpriseClient']){
                $this->formValues['client'] = (int)$this->formValues['enterpriseClient'];
            }

            $client = $clientRepository->find((int)$this->formValues['client']);
            $project->setClient($client);

            $project->setType(\App\Entity\Enums\ProjectType::Parcel);

            $municipality = $municipalityRepository->findOneBy(['name'=>ucfirst('Sin Municipio')]);

            $project->createAutomaticInvestment($municipality);
            $project->createAutomaticBuilding();
            $project->setCurrency($currencyRepository->findOneBy(['code'=>'CUP']));

            $projectRepository->save($project, true);

            $this->pro = new Project();
            if (!is_null($this->modal)) {
                $this->modalManage($project, $successMsg, [
                    'project' => $project->getId()
                ]);

                return $this->redirectToRoute('app_project_edit', ['id'=>$project->getId()], Response::HTTP_SEE_OTHER);
            }

            if ($this->ajax) {
                $this->ajaxManage($project, $successMsg);
                return null;
            }

            $this->addFlash('success', $successMsg);
            return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): ?string
    {
        return 'norender|*';
    }

}
