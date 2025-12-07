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
            ])
//            ->add('subsystemSubType', EntityType::class, [
//                'class' => SubsystemSubType::class,
//                'choice_label' => 'name',
//                'label' => 'Sub tipo:'
//            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $this->onPreSetData($event);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SubsystemTypeSubsystemSubType::class,
        ]);
    }

    /**
     * @param FormEvent $event
     * @return void
     */
    private function onPreSetData(FormEvent $event): void
    {
        /** @var SubsystemTypeSubsystemSubType $stsst */
        $stsst = $event->getData();
        $form = $event->getForm();

        if (is_null($stsst)) {
            $form
                ->add('subsystemSubType', EntityPlusType::class, [
                    'label' => 'Nombre:',
                    'attr' => [
                        'placeholder' => 'Nombre del subtipo'
                    ],
                    'class' => SubsystemSubType::class,
//                'choice_label' => 'name',
//                'choice_value' => 'id',

                    'add' => true,
                    'add_title' => 'Agregar Subtipo',
                    'add_id' => 'modal-load',
                    'add_url' => $this->router->generate('app_subsystem_sub_type_new', ['modal' => 'modal-load']),

//                'data' => $stsst->getSubsystemSubType(),
//                'row_attr' => [
//                    'class' => 'mb-3 row'
//                ]
                ]);
        } else {
            $form
                ->add('subsystemSubType', EntityType::class, [
                    'label' => 'Nombre:',
                    'attr' => [
                        'placeholder' => 'Nombre del subtipo'
                    ],
                    'class' => SubsystemSubType::class,
//                'choice_label' => 'name',
//                'choice_value' => 'id',

//                    'add' => true,
//                    'add_title' => 'Agregar Subtipo',
//                    'add_id' => 'modal-load',
//                    'add_url' => $this->router->generate('app_subsystem_sub_type_new', ['modal' => 'modal-load'/*, 'screen' => $options['screen'*/]),

//                'data' => $stsst->getSubsystemSubType(),
//                'row_attr' => [
//                    'class' => 'mb-3 row'
//                ]
                ]);
        }

        $form
            ->add('subsystemSubType', EntityPlusType::class, [
                'label' => 'Nombre:',
                'attr' => [
                    'placeholder' => 'Nombre del subtipo'
                ],
                'class' => SubsystemSubType::class,
//                'choice_label' => 'name',
//                'choice_value' => 'id',

                'add' => true,
                'add_title' => 'Agregar Subtipo',
                'add_id' => 'modal-load',
                'add_url' => $this->router->generate('app_subsystem_sub_type_new', ['modal' => 'modal-load'/*, 'screen' => $options['screen'*/]),

//                'data' => $stsst->getSubsystemSubType(),
//                'row_attr' => [
//                    'class' => 'mb-3 row'
//                ]
            ]);
    }
}
