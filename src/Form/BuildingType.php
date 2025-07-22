<?php

namespace App\Form;

use App\Entity\Building;
use App\Entity\Constructor;
use App\Entity\Draftsman;
use App\Form\Types\EntityPlusType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class BuildingType extends AbstractType
{
    public function __construct(private readonly RouterInterface $router)
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nombre:',
                'attr' => [
                    'placeholder' => 'Nombre de la obra'
                ]
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options): void {
            $this->onPreSetData($event, $options);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Building::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ],
            'screen' => 'project'//building || project
        ]);
    }

    /**
     * @param FormEvent $event
     * @param array $options
     * @return void
     */
    private function onPreSetData(FormEvent $event, array $options): void
    {
        /** @var Building $building */
        $building = $event->getData();
        $form = $event->getForm();
        $currency = 'CUP';
        $activeConstructor = null;

        //TODO: y si ya de antemano se sabe que proyectista trabajara en la obra?
        if (!is_null($building) && $building->getId()) {
            $form->add('draftsman', EntityType::class, [
                'mapped' => false,
                'class' => Draftsman::class,
                'placeholder' => '-Seleccionar-',
                'label' => 'Proyectista:',
                'required' => false,
                'data' => $building->getActiveDraftsman()
            ]);

            $currency = $building->getProject()->getCurrency()->getCode();
            $activeConstructor = $building->getActiveConstructor();
        }

        $form->add('constructor', EntityPlusType::class, [
            'class' => Constructor::class,
            'choice_label' => 'name',
            'label' => 'Constructora:',
            'mapped' => false,
//                'query_builder' => $this->getOrganismQueryBuilder($options),

            'detail' => true,
            'detail_title' => 'Detalle de la constructora',
            'detail_id' => 'modal-load',
            'detail_url' => $this->router->generate('app_constructor_show', ['id' => 0, 'state' => 'modal']),

            'add' => true,
            'add_title' => 'Agregar Constructora',
            'add_id' => 'modal-load',
            'add_url' => $this->router->generate('app_constructor_new', ['modal' => 'modal-load', 'screen' => $options['screen']]),

            'required' => false,
            'data' => $activeConstructor
        ]);

        $form->add('estimatedValueConstruction', MoneyType::class, [
            'label' => 'Valor estimado de construcción:',
            'attr' => [
                'placeholder' => '0',
                'min' => 0,
                'data-summation-values-target' => 'field',
                'data-currency-target' => 'field'
            ],
            'empty_data' => 0,
            'required' => false,
            'currency' => $currency,
            'html5' => true,
            'input' => 'integer'
        ])
            ->add('estimatedValueEquipment', MoneyType::class, [
                'label' => 'Valor estimado en equipos:',
                'attr' => [
                    'placeholder' => '0',
                    'min' => 0,
                    'data-summation-values-target' => 'field',
                    'data-currency-target' => 'field'
                ],
                'empty_data' => 0,
                'required' => false,
                'currency' => $currency,
                'html5' => true,
                'input' => 'integer'
            ])
            ->add('estimatedValueOther', MoneyType::class, [
                'label' => 'Otros valores estimados:',
                'attr' => [
                    'placeholder' => '0',
                    'min' => 0,
                    'data-summation-values-target' => 'field',
                    'data-currency-target' => 'field'
                ],
                'empty_data' => 0,
                'required' => false,
                'currency' => $currency,
                'html5' => true,
                'input' => 'integer'
            ])
            ->add('approvedValueConstruction', MoneyType::class, [
                'label' => 'Valor aprobado de construcción:',
                'attr' => [
                    'placeholder' => '0',
                    'min' => 0,
                    'data-summation-values-target' => 'field',
                    'data-currency-target' => 'field'
                ],
                'empty_data' => 0,
                'required' => false,
                'currency' => $currency,
                'html5' => true,
                'input' => 'integer'
            ])
            ->add('approvedValueEquipment', MoneyType::class, [
                'label' => 'Valor aprobado en equipos:',
                'attr' => [
                    'placeholder' => '0',
                    'min' => 0,
                    'data-summation-values-target' => 'field',
                    'data-currency-target' => 'field'
                ],
                'empty_data' => 0,
                'required' => false,
                'currency' => $currency,
                'html5' => true,
                'input' => 'integer'
            ])
            ->add('approvedValueOther', MoneyType::class, [
                'label' => 'Otros valores aprobados:',
                'attr' => [
                    'placeholder' => '0',
                    'min' => 0,
                    'data-summation-values-target' => 'field',
                    'data-currency-target' => 'field'
                ],
                'empty_data' => 0,
                'required' => false,
                'currency' => $currency,
                'html5' => true,
                'input' => 'integer'
            ]);
    }
}
