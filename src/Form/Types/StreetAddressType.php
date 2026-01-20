<?php

namespace App\Form\Types;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @template TData of array
 *
 * @extends AbstractType<array>
 */
class StreetAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('street', TextareaType::class, [
                'label' => 'Dirección:',
                'data' => $options['street'],
                'constraints' => $this->getStreetConstraints($options),
                'attr' => [
                    'placeholder' => 'Escriba la dirección',
                ],
            ])
            ->add('address', AddressType::class, [
                'province' => $options['province'],
                'municipality' => $options['municipality'],
                'mapped' => false,
                'live_form' => $options['live_form'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => [
                'novalidate' => 'novalidate',
            ],
            'province' => 0,
            'municipality' => 0,
            'street' => '',
            'live_form' => false,
        ]);

        $resolver->setAllowedTypes('province', ['int']);
        $resolver->setAllowedTypes('municipality', ['int']);
        $resolver->setAllowedTypes('street', ['string']);
        $resolver->setAllowedTypes('live_form', 'bool');
    }

    /**
     * @param array<mixed> $options
     *
     * @return array<mixed>|NotBlank[]
     */
    private function getStreetConstraints(array $options): array
    {
        $constraints = [];

        if ('' === $options['street']) {
            $constraints = [
                new NotBlank(message: 'La dirección está vacía.'),
            ];
        }

        return $constraints;
    }
}
