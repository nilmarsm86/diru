<?php

namespace App\Form;

use App\Entity\Enums\LocalType;
use App\Entity\Enums\UrbanRegulationStructure;
use App\Entity\UrbanRegulation;
use App\Entity\UrbanRegulationType as Type;
use App\Form\Types\EntityPlusType;
use App\Form\Types\TechnicalStatusEnumType;
use App\Form\Types\UrbanRegulationStructureEnumType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

/**
 * @template TData of UrbanRegulation
 * @extends AbstractType<UrbanRegulation>
 */
class UrbanRegulationType extends AbstractType
{
    public function __construct(
        private readonly RouterInterface            $router,
    )
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', null, [
                'label' => 'Código:',
                'attr' => [
                    'placeholder' => 'Código de la regulación'
                ]
            ])
            ->add('description', null, [
                'label' => 'Descripción:',
                'attr' => [
                    'placeholder' => 'Descripción de la regulación'
                ]
            ])
            ->add('data', null, [
                'label' => 'Dato:',
                'attr' => [
                    'placeholder' => 'Dato de la regulación'
                ]
            ])
            ->add('measurementUnit', null, [
                'label' => 'Unidad de medida:',
                'attr' => [
                    'placeholder' => 'Unidad de medida del dato'
                ]
            ])
            ->add('photo', FileType::class, [
                'label' => "Foto:",
                'required' => false,
            ])
            ->add('comment', null, [
                'label' => 'Comentario:',
                'attr' => [
                    'placeholder' => 'Comentario de ayuda'
                ]
            ])
            ->add('legalReference', null, [
                'label' => 'Referencia legal',
                'attr' => [
                    'placeholder' => 'Referencia legal que sustenta la regulación'
                ]
            ])
            ->add('type', EntityPlusType::class, [
                'class' => Type::class,
                'choice_label' => 'name',
                'label' => 'Tipo de regulación',

                'detail' => true,
                'detail_title' => 'Detalle del tipo de regulación',
                'detail_id' => 'modal-load',//'detail_investment_entity',
                'detail_url' => $this->router->generate('app_urban_regulation_type_show', ['id' => 0, 'state' => 'modal']),

                'add' => true,
                'add_title' => 'Agregar tipo',
                'add_id' => 'modal-load',
                'add_url' => $this->router->generate('app_urban_regulation_type_new', ['modal' => 'modal-load']),
//                'constraints' => [
//                    new Assert\NotBlank(message: 'Seleccione o cree el tipo de regulación.')
//                ]
            ])
            ->add('structure', UrbanRegulationStructureEnumType::class, [
                'label' => 'Estructura:'
            ])
//            ->add('projects', EntityType::class, [
//                'class' => Project::class,
//                'choice_label' => 'id',
//                'multiple' => true,
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UrbanRegulation::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ],
            'error_mapping' => [
                'enumStructure' => 'structure',
            ],
        ]);
    }
}
