<?php

namespace App\Form;

use App\Entity\Floor;
use App\Entity\Local;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('number')
            ->add('area')
            ->add('type')
            ->add('height')
            ->add('technicalStatus')
            ->add('type2', EnumType::class, [
                'class' => \App\Entity\Enums\LocalType::class,
            ])
            ->add('name')
            ->add('floor', EntityType::class, [
                'class' => Floor::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Local::class,
        ]);
    }
}
