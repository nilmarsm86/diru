<?php

namespace App\Form;

use App\Entity\ConstructiveAction;
use App\Entity\ConstructiveSystem;
use App\Entity\LocalConstructiveAction;
use App\Form\Types\EntityPlusType;
use App\Form\Types\MoneyPlusType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

/**
 * @template TData of LocalConstructiveAction
 *
 * @extends AbstractType<LocalConstructiveAction>
 */
class LocalConstructiveActionType extends AbstractType
{
    public function __construct(
        private readonly RouterInterface $router,
    ) {
    }

    /**
     * @param FormBuilderInterface<LocalConstructiveAction|null> $builder
     * @param array<string, mixed>                               $options
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
            'data_class' => LocalConstructiveAction::class,
        ]);
    }

    private function onPreSetData(FormEvent $event): void
    {
        /** @var LocalConstructiveAction $localConstructiveAction */
        $localConstructiveAction = $event->getData();
        $form = $event->getForm();

        $currency = 'CUP';
        $local = null;
        if (null !== $localConstructiveAction) {
            $local = $localConstructiveAction->getLocal();
            if (null !== $local) {
                $subSystem = $local->getSubSystem();
                $floor = $subSystem?->getFloor();
                $building = $floor?->getBuilding();
                $project = $building?->getProject();
                $currency = $project?->getCurrency();
                $currency = $currency?->getCode();
            }
        }

        $form->add('price', MoneyPlusType::class, [
            'label' => 'Indicador técnico económico ($/m<sup>2</sup>):',
            'label_html' => true,
            'currency' => $currency,
            'input' => 'integer',
            'divisor' => 100,
            'attr' => [
                'placeholder' => '0',
                'min' => 0,
                'data-usd-currency-target' => 'field',
                'data-controller' => 'money',
                'data-type--money-plus-target' => 'field',
            ],
            'data' => (!is_null($local)) ? $local->getPrice() : 0,
            'grouping' => true,

            'list' => true,
            'list_icon' => 'icon-park-outline:local',
            'list_title' => 'Referencias nacionales',
            'list_id' => 'modal-load',
            'list_url' => $this->router->generate('app_ite_national', [
                'modal' => 'modal-load',
                'screen' => 'modal',
                'amount' => 100,
            ]),

            'add' => true,
            'add_icon' => 'streamline-freehand:currency-international-currency',
            'add_title' => 'Referencias internacionales',
            'add_id' => 'modal-load',
            'add_url' => $this->router->generate('app_ite_international', [
                'modal' => 'modal-load',
                'screen' => 'modal',
                'amount' => 100,
            ]),
        ]);
    }
}
