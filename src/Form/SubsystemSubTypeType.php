<?php

namespace App\Form;

use App\Entity\SubsystemSubType;
use App\Entity\SubsystemType;
use App\Form\Types\EntityPlusType;
use App\Repository\SubsystemTypeRepository;
use Closure;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class SubsystemSubTypeType extends AbstractType
{
    public function __construct(private readonly RouterInterface $router)
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['screen'] === 'type') {
            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options): void {
                $this->onPreSetData($event, $options);
            });
        }

        if ($options['screen'] === 'subtype') {
            $builder
                ->add('name', null, [
                    'label' => 'Nombre:',
                    'attr' => [
                        'placeholder' => 'Nombre del subtipo'
                    ]
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SubsystemSubType::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ],
            'modal' => null,
            'screen' => 'type'//subtype || type
        ]);

        $resolver->setAllowedTypes('modal', ['null', 'string']);
        $resolver->setAllowedTypes('screen', 'string');
    }

    /**
     * @param FormEvent $event
     * @param array $options
     * @return void
     */
    private function onPreSetData(FormEvent $event, array $options): void
    {
        $subsystemSubType = $event->getData();
        $form = $event->getForm();

        $form
            ->add('name', EntityPlusType::class, [
                'label' => 'Nombre:',
                'attr' => [
                    'placeholder' => 'Nombre del subtipo'
                ],
                'class' => SubsystemSubType::class,
                'choice_label' => 'name',
                'choice_value' => 'id',

                'add' => true,
                'add_title' => 'Agregar Subtipo',
                'add_id' => 'modal-load',
                'add_url' => $this->router->generate('app_subsystem_sub_type_new', ['modal' => 'modal-load'/*, 'screen' => $options['screen'*/]),

                'data' => $subsystemSubType,
//                'row_attr' => [
//                    'class' => 'mb-3 row'
//                ]
            ]);
    }
}
