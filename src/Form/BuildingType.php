<?php

namespace App\Form;

use App\Entity\Building;
use App\Entity\Constructor;
use App\Entity\Investment;
use App\Entity\Project;
use App\Form\Types\EntityPlusType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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
            ->add('estimatedValueConstruction', IntegerType::class, [
                'label' => 'Valor estimado de construcción:',
                'attr' => [
                    'placeholder' => '0',
                    'min' => 0
                ],
                'empty_data' => 0,
                'required' => false
            ])
            ->add('estimatedValueEquipment', IntegerType::class, [
                'label' => 'Valor estimado en equipos:',
                'attr' => [
                    'placeholder' => '0',
                    'min' => 0
                ],
                'empty_data' => 0,
                'required' => false
            ])
            ->add('estimatedValueOther', IntegerType::class, [
                'label' => 'Otros valores estimados:',
                'attr' => [
                    'placeholder' => '0',
                    'min' => 0
                ],
                'empty_data' => 0,
                'required' => false
            ])
            ->add('approvedValueConstruction', IntegerType::class, [
                'label' => 'Valor aprobado de construcción:',
                'attr' => [
                    'placeholder' => '0',
                    'min' => 0
                ],
                'empty_data' => 0,
                'required' => false
            ])
            ->add('approvedValueEquipment', IntegerType::class, [
                'label' => 'Valor aprobado en equipos:',
                'attr' => [
                    'placeholder' => '0',
                    'min' => 0
                ],
                'empty_data' => 0,
                'required' => false
            ])
            ->add('approvedValueOther', IntegerType::class, [
                'label' => 'Otros valores aprobados:',
                'attr' => [
                    'placeholder' => '0',
                    'min' => 0
                ],
                'empty_data' => 0,
                'required' => false
            ])
            ->add('constructor', EntityPlusType::class, [
                'class' => Constructor::class,
                'choice_label' => 'name',
                'label' => 'Constructora:',
//                'query_builder' => $this->getOrganismQueryBuilder($options),

                'detail' => true,
                'detail_title' => 'Detalle de la constructora',
                'detail_id' => 'modal-load',
                'detail_url' => $this->router->generate('app_constructor_show', ['id' => 0, 'state' => 'modal']),

                'add' => true,
                'add_title' => 'Agregar Constructora',
                'add_id' => 'modal-load',
                'add_url' => $this->router->generate('app_constructor_new', ['modal' => 'modal-load']),

                'required' => false
            ])
            ->add('project', EntityPlusType::class, [
                'class' => Project::class,
                'choice_label' => 'name',
                'label' => 'Proyecto:',
                'placeholder' => '-Seleccionar-',

                'detail' => true,
                'detail_title' => 'Detalle del Proyecto',
                'detail_id' => 'modal-load',
                'detail_url' => $this->router->generate('app_project_show', ['id' => 0, 'state' => 'modal']),

                'add' => true,
                'add_title' => 'Agregar Proyecto',
                'add_id' => 'modal-load',
                'add_url' => $this->router->generate('app_project_new', ['modal' => 'modal-load']),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Building::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}
