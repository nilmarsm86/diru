<?php

namespace App\Form;

use App\Entity\ConstructiveAction;
use App\Entity\ConstructiveSystem;
use App\Entity\LandNetworkConnectionConstructiveAction;
use App\Form\Types\EntityPlusType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

/**
 * @template TData of LandNetworkConnectionConstructiveAction
 *
 * @extends AbstractType<LandNetworkConnectionConstructiveAction>
 */
class LandNetworkConnectionConstructiveActionType extends AbstractType
{
    public function __construct(
        private readonly RouterInterface $router,
    ) {
    }

    /**
     * @param FormBuilderInterface<LandNetworkConnectionConstructiveAction|null> $builder
     * @param array<string, mixed>                                               $options
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('constructiveAction', EntityType::class, [
                'class' => ConstructiveAction::class,
                'choice_label' => 'name',
                'label' => 'Tipo:',
                'placeholder' => '-Seleccione-',
                'group_by' => fn (ConstructiveAction $constructiveAction, int $key, string $value) => $constructiveAction->getType()::getLabelFrom($constructiveAction->getType()),
            ])
            ->add('constructiveSystem', EntityPlusType::class, [
                'class' => ConstructiveSystem::class,
                'choice_label' => 'name',
                'label' => 'Sistema constructivo:',
                'placeholder' => '-Seleccione-',

                'add' => true,
                'add_title' => 'Agregar Sistema Constructivo',
                'add_id' => 'modal-load',
                'add_url' => $this->router->generate('app_constructive_system_new', ['modal' => 'modal-load']),
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $this->onPreSetData($event);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LandNetworkConnectionConstructiveAction::class,
        ]);
    }

    private function onPreSetData(FormEvent $event): void
    {
        /** @var LandNetworkConnectionConstructiveAction $landNetworkConnectionConstructiveAction */
        $landNetworkConnectionConstructiveAction = $event->getData();
        $form = $event->getForm();

        $currency = 'CUP';
        if (null !== $landNetworkConnectionConstructiveAction) {
            $landNetworkConnection = $landNetworkConnectionConstructiveAction->getLandNetworkConnection();
            if (null !== $landNetworkConnection) {
                $building = $landNetworkConnection->getBuilding();
                $project = $building?->getProject();
                $currency = $project?->getCurrency();
                $currency = $currency?->getCode();
            }
        }

        $form->add('price', MoneyType::class, [
            'label' => 'Precio:',
            'currency' => $currency,
            'input' => 'integer',
            'divisor' => 100,
            'attr' => [
                'placeholder' => 0,
                'min' => 0,
                'data-usd-currency-target' => 'field',
                'data-controller' => 'money',
            ],
            'empty_data' => 0,
            'grouping' => true,
        ]);
    }
}
