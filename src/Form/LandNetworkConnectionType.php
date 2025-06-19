<?php

namespace App\Form;

use App\Entity\LandNetworkConnection;
use App\Entity\NetworkConnection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LandNetworkConnectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('explanation', null, [
                'label' => 'Explicación:'
            ])
            ->add('networkConnection', EntityType::class, [
                'class' => NetworkConnection::class,
                'choice_label' => 'name',
                'label' => 'Tipo:',
                'placeholder' => '-Seleccinar-',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LandNetworkConnection::class,
        ]);
    }
}
