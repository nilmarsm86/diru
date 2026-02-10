<?php

namespace App\Form;

use App\Entity\UrbanizationEstimate;
use App\Form\Types\TrixEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template TData of UrbanizationEstimate
 *
 * @extends AbstractType<UrbanizationEstimate>
 */
class UrbanizationEstimateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currency = 'CUP';

        $builder
            ->add('concept', null, [
                'label' => 'Por concepto de:',
            ])
            ->add('measurementUnit', null, [
                'label' => 'Unidad de medida:',
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Precio:',
                'attr' => [
                    'placeholder' => '0',
                    'min' => 0,
                    'data-controller' => 'money',
                ],
                'empty_data' => 0,
                //                'required' => false,
                'currency' => $currency,
                'input' => 'integer',
                'divisor' => 100,
                'grouping' => true,
            ])
            ->add('quantity', null, [
                'label' => 'Cantidad:',
            ])
            ->add('comment', TrixEditorType::class, [
                'label' => 'Comentario:',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UrbanizationEstimate::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
