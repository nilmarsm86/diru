<?php

namespace App\Form\Types;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class StreetAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('street', TextareaType::class, [
                'label' => 'Dirección:',
                'mapped' => false,
                'data' => $options['street'],
                'constraints' => $this->getStreetConstraints($options),
            ])
            ->add('address', AddressType::class, [
                'province' => $options['province'],
                'municipality' => $options['municipality'],
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => [
                'novalidate' => 'novalidate'
            ],
            'province' => 0,
            'municipality' => 0,
            'street' => '',
        ]);

        $resolver->setAllowedTypes('province', ['int']);
        $resolver->setAllowedTypes('municipality', ['int']);
        $resolver->setAllowedTypes('street', ['string']);
    }

    /**
     * @param array $options
     * @return array|NotBlank[]
     */
    private function getStreetConstraints(array $options): array
    {
        $constraints = [];

        if ($options['street'] === '') {
            $constraints = [
                new NotBlank(message: 'Establezca la dirección.')
            ];
        }

        return $constraints;
    }

}
