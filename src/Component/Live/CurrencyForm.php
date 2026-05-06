<?php

namespace App\Component\Live;

use App\Component\Live\Traits\ComponentForm;
use App\Entity\Currency;
use App\Form\CurrencyType;
use App\Repository\CurrencyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent(template: 'component/live/name_and_code_form.html.twig')]
final class CurrencyForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentToolsTrait;
    use ComponentForm;
    use LiveCollectionTrait;

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    public ?Currency $cur = null;

    #[LiveProp]
    public ?string $modal = null;

    #[LiveProp]
    public bool $ajax = false;

    #[LiveProp]
    public Currency $entity;

    public function mount(?Currency $cur = null): void
    {
        $this->cur = (is_null($cur)) ? new Currency() : $cur;
        $this->entity = $this->cur;
    }

    /**
     * @return FormInterface<Currency>
     */
    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(CurrencyType::class, $this->cur);
    }

    #[LiveAction]
    public function save(CurrencyRepository $currencyRepository): ?Response
    {
        $successMsg = (is_null($this->cur?->getId())) ? 'Se ha agregado la moneda.' : 'Se ha modificado la moneda.'; // TODO: personalizar los mensajes

        $this->submitForm();

        if ($this->isSubmitAndValid()) {
            /** @var Currency $currency */
            $currency = $this->getForm()->getData();

            $currencyRepository->save($currency, true);

            $this->cur = new Currency();
            $this->entity = $this->cur;
            if (!is_null($this->modal)) {
                $this->modalManage($currency, 'Se ha seleccionado la nueva moneda agregada.', [
                    'currency' => $currency->getId(),
                ]);

                return null;
            }

            if ($this->ajax) {
                $this->ajaxManage($currency, $successMsg);

                return null;
            }

            $this->addFlash('success', $successMsg);

            return $this->redirectToRoute('app_currency_index', [], Response::HTTP_SEE_OTHER);
        }

        return null;
    }

    /** @SuppressWarnings(PHPMD.UnusedPrivateMethod) */
    private function getDataModelValue(): string
    {
        return 'norender|*';
    }
}
