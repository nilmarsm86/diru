<?php

namespace App\Form\Types;

use App\Form\DataTransformer\ArrayToCsvTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template TData of array
 *
 * @extends AbstractType<array>
 */
class SimpleArrayTextareaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var string $separator */
        $separator = $options['separator'];
        $builder->addModelTransformer(
            new ArrayToCsvTransformer($separator)
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'separator' => ',',
            'required' => false,
            'attr' => [
                'rows' => 3,
                'placeholder' => 'Ingrese valores separados por comas (ej: valor1, valor2, valor3)',
            ],
            'help' => 'Separe los valores con comas',
        ]);

        $resolver->setAllowedTypes('separator', 'string');
    }

    public function getParent(): string
    {
        return TextareaType::class;
    }
}
