<?php

namespace App\Form;

use App\Entity\Municipality;
use App\Entity\Province;
use App\Form\Types\EntityPlusType;
use App\Repository\ProvinceRepository;
use Closure;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class MunicipalityType extends AbstractType
{
    public function __construct(private readonly RouterInterface $router)
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $provinceAttr = [
            'class' => Province::class,
            'choice_label' => 'name',
            'label' => 'Provincia:',
            'attr' => [
                'data-model' => 'province',
            ],
            'query_builder' => $this->getProvinceQueryBuilder(),
        ];

        $builder
            ->add('name', null, [
                'label' => 'Nombre:',
                'attr' => [
                    'placeholder' => 'Nombre del municipio'
                ]
            ]);

        if (is_null($options['modal'])) {
            $builder->add('province', EntityPlusType::class, [
                    'add' => true,
                    'add_title' => 'Agregar Provincia',
                    'add_id' => 'modal-load',
                    'add_url' => $this->router->generate('app_province_new', ['modal' => 'modal-load']),
            ]+$provinceAttr);
        } else {
            $builder->add('province', EntityType::class, []+$provinceAttr);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Municipality::class,
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
    private function getProvinceQueryBuilder(): Closure
    {
        return function (ProvinceRepository $provinceRepository): QueryBuilder|array {
            return $provinceRepository->findProvincesForForm();
        };
    }
}
