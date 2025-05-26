<?php

namespace App\Form;

use App\Entity\Building;
use App\Entity\Constructor;
use App\Entity\Investment;
use App\Form\Types\EntityPlusType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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
            ])
            ->add('estimatedValueConstruction', null, [
                'label' => 'Valor estimado de construcción:',
                'attr' => [
                    'placeholder' => '0'
                ],
                'data' => 0
            ])
            ->add('estimatedValueEquipment', null, [
                'label' => 'Valor estimado en equipos:',
                'attr' => [
                    'placeholder' => '0'
                ],
                'data' => 0
            ])
            ->add('estimatedValueOther', null, [
                'label' => 'Otros valores estimados:',
                'attr' => [
                    'placeholder' => '0'
                ],
                'data' => 0
            ])
            ->add('approvedValueConstruction', null, [
                'label' => 'Valor aprobado de construcción:',
                'attr' => [
                    'placeholder' => '0'
                ],
                'data' => 0
            ])
            ->add('approvedValueEquipment', null, [
                'label' => 'Valor aprobado en equipos:',
                'attr' => [
                    'placeholder' => '0'
                ],
                'data' => 0
            ])
            ->add('approvedValueOther', null, [
                'label' => 'Otros valores aprobados:',
                'attr' => [
                    'placeholder' => '0'
                ],
                'data' => 0
            ])
            ->add('constructor', EntityPlusType::class, [
                'class' => Constructor::class,
                'choice_label' => 'name',
                'label' => 'Constructora:',
                'placeholder' => '-Seleccionar-',
//                'query_builder' => $this->getOrganismQueryBuilder($options),
                'modal_id' => '#add-constructor',
                'detail' => true,
                'detail_title' => 'Detalle de la constructora',
                'detail_id' => 'detail_constructor_entity',
                'detail_loading' => 'Cargando detalles de la constructora...',
                'detail_url' => $this->router->generate('app_constructor_show', ['id' => 0, 'state' => 'modal'])
            ])
            ->add('investment', EntityPlusType::class, [
                'class' => Investment::class,
                'choice_label' => 'name',
                'label' => 'Inversión:',
                'placeholder' => '-Seleccionar-',
                'modal_id' => '#add-investment',
                'detail' => true,
                'detail_title' => 'Detalle de la Inversión',
                'detail_id' => 'detail_investment_entity',
                'detail_loading' => 'Cargando detalles de la inversión...',
                'detail_url' => $this->router->generate('app_investment_show', ['id' => 0, 'state' => 'modal'])
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Building::class,
        ]);
    }
}
