<?php

namespace App\Form;

use App\Entity\Land;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('landArea')
            ->add('occupiedArea')
            ->add('perimeter')
            ->add('photo')
            ->add('microlocalization')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Land::class,
        ]);
    }
}
