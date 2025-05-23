<?php

namespace App\Form;

use App\Entity\Municipality;
use App\Entity\Province;
use App\Form\Types\EntityPlusType;
use Closure;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MunicipalityType extends AbstractType
{
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
                'modal_id' => '#add-province',
                'path' => '',//como el formulario es live-component cuando se agregar el dato se recarga y trae el dato nuevo por eso se puede dejar vacio
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
        return function (EntityRepository $er): QueryBuilder|array {
            return $er->createQueryBuilder('p')->orderBy('p.name', 'ASC');
        };
    }
}
