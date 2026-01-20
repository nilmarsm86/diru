<?php

namespace App\Form;

use App\Entity\SubsystemSubType;
use App\Form\Types\EntityPlusType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

/**
 * @template TData of SubsystemSubType
 *
 * @extends AbstractType<SubsystemSubType>
 */
class SubsystemSubTypeType extends AbstractType
{
    public function __construct(private readonly RouterInterface $router)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ('type' === $options['screen']) {
            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
                $this->onPreSetData($event);
            });
        }

        if ('subtype' === $options['screen']) {
            $builder
                ->add('name', null, [
                    'label' => 'Nombre:',
                    'attr' => [
                        'placeholder' => 'Nombre del subtipo',
                    ],
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SubsystemSubType::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
            'modal' => null,
            'screen' => 'type', // subtype || type
        ]);

        $resolver->setAllowedTypes('modal', ['null', 'string']);
        $resolver->setAllowedTypes('screen', 'string');
    }

    private function onPreSetData(FormEvent $event): void
    {
        $subsystemSubType = $event->getData();
        $form = $event->getForm();

        $form
            ->add('name', EntityPlusType::class, [
                'label' => 'Nombre:',
                'attr' => [
                    'placeholder' => 'Nombre del subtipo',
                ],
                'class' => SubsystemSubType::class,

                'add' => true,
                'add_title' => 'Agregar Subtipo',
                'add_id' => 'modal-load',
                'add_url' => $this->router->generate('app_subsystem_sub_type_new', ['modal' => 'modal-load']),

                'data' => $subsystemSubType,
            ]);
    }
}
