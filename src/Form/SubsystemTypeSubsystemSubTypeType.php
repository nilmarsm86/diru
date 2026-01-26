<?php

namespace App\Form;

use App\Entity\SubsystemSubType;
use App\Entity\SubsystemType;
use App\Entity\SubsystemTypeSubsystemSubType;
use App\Form\Types\EntityPlusType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

/**
 * @template TData of SubsystemTypeSubsystemSubType
 *
 * @extends AbstractType<SubsystemTypeSubsystemSubType>
 */
class SubsystemTypeSubsystemSubTypeType extends AbstractType
{
    public function __construct(private readonly RouterInterface $router)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subsystemType', EntityType::class, [
                'class' => SubsystemType::class,
                'choice_label' => 'name',
                'label' => 'Nombre:',
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options): void {
            $this->onPreSetData($event, $options);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SubsystemTypeSubsystemSubType::class,
            'from' => 'type',
        ]);

        $resolver->setAllowedTypes('from', 'string');
    }

    /**
     * @param array<mixed> $options
     */
    private function onPreSetData(FormEvent $event, array $options): void
    {
        /** @var SubsystemTypeSubsystemSubType $stsst */
        $stsst = $event->getData();
        $form = $event->getForm();

        if (is_null($stsst)) {
            if ('type' === $options['from']) {
                $form
                    ->add('subsystemSubType', EntityPlusType::class, [
                        'label' => 'Nombre:',
                        'attr' => [
                            'placeholder' => 'Nombre del subtipo',
                        ],
                        'class' => SubsystemSubType::class,

                        'add' => true,
                        'add_title' => 'Agregar Subtipo',
                        'add_id' => 'modal-load',
                        'add_url' => $this->router->generate('app_subsystem_sub_type_new', ['modal' => 'modal-load']),
                    ]);
            }
        } else {
            if ('type' === $options['from']) {
                $form
                    ->add('subsystemSubType', EntityType::class, [
                        'label' => 'Nombre:',
                        'attr' => [
                            'placeholder' => 'Nombre del subtipo',
                        ],
                        'class' => SubsystemSubType::class,
                    ]);

                $form
                    ->add('subsystemSubType', EntityPlusType::class, [
                        'label' => 'Nombre:',
                        'attr' => [
                            'placeholder' => 'Nombre del subtipo',
                        ],
                        'class' => SubsystemSubType::class,

                        'add' => true,
                        'add_title' => 'Agregar Subtipo',
                        'add_id' => 'modal-load',
                        'add_url' => $this->router->generate('app_subsystem_sub_type_new', ['modal' => 'modal-load'/* , 'screen' => $options['screen' */]),
                    ]);
            }
        }
    }
}
