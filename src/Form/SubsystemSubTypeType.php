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
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class SubsystemSubTypeType extends AbstractType
{
    public function __construct(private readonly RouterInterface $router)
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $provinceAttr = [
            'class' => SubsystemType::class,
            'choice_label' => 'name',
            'label' => 'Tipo:',
            'attr' => [
                'data-model' => 'subsystemType',
            ],
            //'query_builder' => $this->getProvinceQueryBuilder(),
        ];

        //si es nuevo
        $builder
            ->add('name', null, [
                'label' => 'Nombre:',
                'attr' => [
                    'placeholder' => 'Nombre del subtipo'
                ]
            ]);

//        $builder
//            ->add('name', ChoiceType::class, [
//                'label' => 'Nombre:',
//                'attr' => [
//                    'placeholder' => 'Nombre del subtipo'
//                ]
//            ]);



//        if (is_null($options['modal'])) {
//            $builder->add('subsystemTypes', EntityPlusType::class, [
//                    'add' => true,
//                    'add_title' => 'Agregar tipo',
//                    'add_id' => 'modal-load',
//                    'add_url' => $this->router->generate('app_subsystem_type_new', ['modal' => 'modal-load']),
//            ]+$provinceAttr);
//        } else {
//            $builder->add('subsystemTypes', EntityType::class, []+$provinceAttr);
//        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SubsystemSubType::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ],
            'modal' => null
        ]);

        $resolver->setAllowedTypes('modal', ['null', 'string']);
    }

    /**
     * @return Closure

    private function getProvinceQueryBuilder(): Closure
    {
        return function (ProvinceRepository $provinceRepository): QueryBuilder|array {
            return $provinceRepository->findProvincesForForm();
        };
    }*/
}
