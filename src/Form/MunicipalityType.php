<?php

namespace App\Form;

use App\Entity\Municipality;
use App\Entity\Province;
use App\Form\Types\EntityPlusType;
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
        $builder
            ->add('name', null, [
                'label' => 'Nombre:'
            ]);

        if (is_null($options['modal'])) {
            $builder->add('province', EntityPlusType::class, [
                'class' => Province::class,
                'choice_label' => 'name',
                'label' => 'Provincia:',
                'modal_id' => '#add-province',
                'attr' => [
                    'data-model' => 'province'
                ],
                'path' => 'app_province_options'
            ]);
        } else {
            $builder->add('province', EntityType::class, [
                'class' => Province::class,
                'choice_label' => 'name',
                'label' => 'Provincia:',
//                'modal_id' => '#add-province',
                'attr' => [
                    'data-model' => 'province'
                ],
//                'path' => 'app_province_options'
                'query_builder' => $this->getProvinceQueryBuilder(),
            ]);
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
     * @return \Closure
     */
    private function getProvinceQueryBuilder(): \Closure
    {
        return function (EntityRepository $er): QueryBuilder|array {
            return $er->createQueryBuilder('p')->orderBy('p.name');
        };
    }
}
