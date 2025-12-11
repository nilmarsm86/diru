<?php

namespace App\Form;

use App\Entity\NetworkConnection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template TData of NetworkConnection
 *
 * @extends AbstractType<NetworkConnection>
 */
class NetworkConnectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nombre:',
                'attr' => [
                    'placeholder' => 'Nombre de la conexión de red',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NetworkConnection::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
