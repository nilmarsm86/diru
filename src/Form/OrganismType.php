<?php

namespace App\Form;

use App\Entity\Organism;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template TData of Organism
 *
 * @extends AbstractType<Organism>
 */
class OrganismType extends AbstractType
{
    /**
     * @param FormBuilderInterface<Organism|null> $builder
     * @param array<string, mixed>                $options
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nombre:',
                'attr' => [
                    'placeholder' => 'Nombre del organismo',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Organism::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
