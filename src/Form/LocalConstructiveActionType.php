<?php

namespace App\Form;

use App\Entity\Building;
use App\Entity\ConstructiveAction;
use App\Entity\ConstructiveSystem;
use App\Entity\Currency;
use App\Entity\Floor;
use App\Entity\LocalConstructiveAction;
use App\Entity\Project;
use App\Entity\SubSystem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template TData of LocalConstructiveAction
 * @extends AbstractType<LocalConstructiveAction>
 */
class LocalConstructiveActionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('constructiveAction', EntityType::class, [
                'class' => ConstructiveAction::class,
                'choice_label' => 'name',
                'label' => 'Tipo:',
                'placeholder' => '-Seleccione-',
                'group_by' => fn(ConstructiveAction $constructiveAction, int $key, string $value) => $constructiveAction->getType()->getLabelFrom($constructiveAction->getType()),
            ])
            ->add('constructiveSystem', EntityType::class, [
                'class' => ConstructiveSystem::class,
                'choice_label' => 'name',
                'label' => 'Sistema constructivo:',
                'placeholder' => '-Seleccione-'
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

    /**
     * @param FormEvent $event
     * @return void
     */
    private function onPreSetData(FormEvent $event): void
    {
//        \Locale::setDefault('en');

        /** @var LocalConstructiveAction $localConstructiveAction */
        $localConstructiveAction = $event->getData();
        $form = $event->getForm();

        $currency = 'CUP';
        $local = null;
        if ($localConstructiveAction) {
            $local = $localConstructiveAction->getLocal();
            if ($local) {
                $subSystem = $local->getSubSystem();
                $floor = $subSystem?->getFloor();
                $building = $floor?->getBuilding();
                $project = $building?->getProject();
                $currency = $project?->getCurrency();
                $currency = $currency?->getCode();
            }
        }

        $form->add('price', MoneyType::class, [
            'label' => 'Indicador técnico económico ($/m<sup>2</sup>):',
            'label_html' => true,
            'currency' => $currency,
//            'html5' => true,
            'input' => 'integer',
            'divisor' => 100,
            'attr' => [
                'placeholder' => '0',
                'min' => 0,
                'data-usd-currency-target' => 'field',
                'data-controller' => 'money'
            ],
            'data' => (!is_null($local)) ? $local->getPrice() : 0,
            'grouping' => true,
        ]);
    }
}
