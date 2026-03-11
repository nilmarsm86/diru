<?php

namespace App\Form;

use App\Entity\ConstructiveAction;
use App\Form\Types\ConstructiveActionTypeEnumType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template TData of ConstructiveAction
 *
 * @extends AbstractType<ConstructiveAction>
 */
class ConstructiveActionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ConstructiveActionTypeEnumType::class, [
                'label' => 'Tipo:',
            ])
            ->add('name', null, [
                'label' => 'Nombre:',
                'attr' => [
                    'placeholder' => 'Nombre de la acción constructiva',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ConstructiveAction::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
