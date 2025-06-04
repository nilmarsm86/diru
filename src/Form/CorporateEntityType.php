<?php

namespace App\Form;

use App\Entity\CorporateEntity;
use App\Entity\Organism;
use App\Form\Types\AddressType;
use App\Form\Types\CorporateEntityTypeEnumType;
use App\Form\Types\EntityPlusType;
use Closure;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class CorporateEntityType extends AbstractType
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
                    'placeholder' => 'Nombre de la entidad corporativa'
                ]
            ])
            ->add('code', null, [
                'label' => 'Código de empresa:',
                'attr' => [
                    'placeholder' => 'Código de empresa'
                ]
            ])
            ->add('nit', null, [
                'label' => 'NIT:',
//                'help' => 'Número de Identificación Tributaria',
                'attr' => [
                    'placeholder' => 'Número de Identificación Tributaria',
                ]
            ])
            ->add('type', CorporateEntityTypeEnumType::class, [
                'label' => 'Tipo de entidad:',
            ])
            ->add('address', AddressType::class, [
                'province' => $options['province'],
                'municipality' => $options['municipality'],
                'mapped' => false,
                'live_form' => $options['live_form'],
                'modal' => $options['modal']
            ]);

        $organismAttr = [
            'class' => Organism::class,
            'choice_label' => 'name',
            'label' => 'Organismo:',
            'placeholder' => '-Seleccione-',
            'attr' => [
//                    'data-model' => 'norender|organism',
            ],
            'query_builder' => $this->getOrganismQueryBuilder($options),
        ];

        if (is_null($options['modal'])) {
            $builder
                ->add('organism', EntityPlusType::class, [
//                    'modal_id' => '#add-organism',
//                    'path' => ''//esta vacio pq el form esta dentro de un live-component
                        'add' => true,
                        'add_title' => 'Agregar Organismo',
                        'add_id' => 'modal-load',
                        'add_url' => $this->router->generate('app_organism_new', ['modal' => 'modal-load']),
                    ] + $organismAttr);
        } else {
            $builder->add('organism', EntityType::class, [] + $organismAttr);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CorporateEntity::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ],
            'province' => 0,
            'municipality' => 0,
            'error_mapping' => [
                'enumType' => 'type',
            ],
            'live_form' => false,
            'modal' => null
        ]);

        $resolver->setAllowedTypes('province', 'int');
        $resolver->setAllowedTypes('municipality', 'int');
        $resolver->setAllowedTypes('live_form', 'bool');
        $resolver->setAllowedTypes('modal', ['null', 'string']);
    }

    /**
     * @param array $options
     * @return Closure
     */
    private function getOrganismQueryBuilder(array $options): Closure
    {
        return function (EntityRepository $er) use ($options): QueryBuilder|array {
            return $er->createQueryBuilder('o')->orderBy('o.name');
        };
    }
}
