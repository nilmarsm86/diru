<?php

namespace App\Form;

use App\Entity\SubsystemSubType;
use App\Entity\SubsystemType;
use App\Form\Types\EntityPlusType;
use App\Repository\ProvinceRepository;
use App\Repository\SubsystemTypeRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
        $typeAttr = [
            'class' => SubsystemType::class,
            'choice_label' => 'name',
            'label' => 'Tipo:',
            'attr' => [
                'data-model' => 'tipo',
            ],
//            'query_builder' => $this->getProvinceQueryBuilder(),
        ];

        $builder
            ->add('name', null, [
                'label' => 'Nombre:',
                'attr' => [
                    'placeholder' => 'Nombre del sub tipo'
                ]
            ]);
        if (is_null($options['modal'])) {
            $builder->add('subsystemType', EntityPlusType::class, [
                    'add' => true,
                    'add_title' => 'Agregar tipo de subsistema',
                    'add_id' => 'modal-load',
                    'add_url' => $this->router->generate('app_subsystem_type_new', ['modal' => 'modal-load']),
                ]+$typeAttr);
        } else {
            $builder->add('subsystemType', EntityType::class, []+$typeAttr);
        }
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
     */
    private function getSubsystemTypeQueryBuilder(): Closure
    {
        return function (SubsystemTypeRepository $subsystemTypeRepository): QueryBuilder|array {
            return $subsystemTypeRepository->findProvincesForForm();
        };
    }
}
