<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template TData of array
 *
 * @extends AbstractType<array>
 */
class ExtraFloorForReplyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('floor', ChoiceType::class, [
                'label' => 'Agregar plantas extras a la obra replicada:',
                'placeholder' => '-Seleccinar-',
                'choices' => array_combine(range(1, 50), range(1, 50)),
                'required' => false,
                //                'attr' => [] + $disabled,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
