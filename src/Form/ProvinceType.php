<?php

namespace App\Form;

use App\Entity\Province;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

/**
 * @template TData of Province
 * @extends AbstractType<Province>
 */
class ProvinceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nombre:',
                'attr' => [
                    'placeholder' => 'Nombre de la provincia'
                ]
            ])
            ->add('municipalities', LiveCollectionType::class, [
                'entry_type' => MunicipalityType::class,
                'button_delete_options' => [
                    'label_html' => true
                ],
                'error_bubbling' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Province::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ],
        ]);
    }
}
