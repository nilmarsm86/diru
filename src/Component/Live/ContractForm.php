<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Contract;
use App\Form\ContractType;
use App\Repository\ContractRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'component/live/contract_form.html.twig')]
final class ContractForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentForm;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?Contract $con = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    public function mount(?Contract $con = null): void
    {
        $this->con = (is_null($con)) ? new Contract() : $con;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ContractType::class, $this->con);
    }

    /**
     * @throws \Exception
     */
    #[LiveAction]
    public function save(ContractRepository $contractRepository): ?Response
    {
        $successMsg = (is_null($this->con?->getId())) ? 'Se ha agregado el contrato.' : 'Se ha modificado el contrato.'; // TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var Contract $contract */
            $contract = $this->getForm()->getData();

            $contractRepository->save($contract, true);

            $this->con = new Contract();
            if (!is_null($this->modal)) {
                $this->modalManage($contract, 'Se ha seleccionado el nuevo contrato agregado.', [
                    'contract' => $contract->getId(),
                ]);

                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($contract, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('app_contract_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }
}
