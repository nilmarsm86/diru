<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Component\Twig\Modal\Modal;
use App\Entity\LocationZone;
use App\Entity\NetworkConnection;
use App\Entity\Organism;
use App\Form\LocationZoneType;
use App\Form\NetworkConnectionType;
use App\Form\OrganismType;
use App\Repository\LocationZoneRepository;
use App\Repository\NetworkConnectionRepository;
use App\Repository\OrganismRepository;
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

#[AsLiveComponent(template: 'partials/live_component/only_name_form.html.twig')]
final class NetworkConnectionForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?NetworkConnection $nc = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp]
    public NetworkConnection $entity;

    public function mount(?NetworkConnection $nc = null): void
    {
        $this->nc = (is_null($nc)) ? new NetworkConnection() : $nc;
        $this->entity = $this->nc;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(NetworkConnectionType::class, $this->nc);
    }

    /**
     * @throws Exception
     */
    #[LiveAction]
    public function save(NetworkConnectionRepository $networkConnectionRepository): ?Response
    {
        $successMsg = (is_null($this->nc->getId())) ? 'Se ha agregado la conexión de red.' : 'Se ha modificado la conexión de red.';//TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var NetworkConnection $nc */
            $nc = $this->getForm()->getData();

            $networkConnectionRepository->save($nc, true);

            $this->nc = new NetworkConnection();
            $this->entity = $this->nc;
            if (!is_null($this->modal)) {
                $this->modalManage($nc, 'Seleccione la nueva zona de ubicación.', [
                    'locationZone' => $nc->getId()
                ]);
                return null;
            }

            if ($this->ajax) {
//                $this->ajaxManage($successMsg);
                $this->ajaxManage($nc, $successMsg);
                return null;
            }

            $this->addFlash('success', $successMsg);
            return $this->redirectToRoute('app_network_connection_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    private function getDataModelValue(): ?string
    {
        return 'norender|*';
    }

}
