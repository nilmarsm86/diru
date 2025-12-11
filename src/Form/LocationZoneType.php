<?php

namespace App\Form;

use App\Entity\LocationZone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @template TData of LocationZone
 *
 * @extends AbstractType<LocationZone>
 */
class LocationZoneType extends AbstractType
{
    private int $min = 1;
    private int $max = 100;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', IntegerType::class, [
                'label' => 'Nombre:',
                'attr' => [
                    'placeholder' => 'Número de la zona de ubicación',
                    'min' => $this->min,
                    'max' => $this->max,
                ],
                'constraints' => [
                    new Assert\Positive(message: 'El número debe ser positivo'),
                    new Assert\Range(
                        notInRangeMessage: 'El rango debe estar entre {{ min }} y {{ max }}.',
                        min: $this->min,
                        max: $this->max,
                    ),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LocationZone::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
