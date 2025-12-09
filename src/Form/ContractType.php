<?php

namespace App\Form;

use App\Entity\Contract;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template TData of Contract
 * @extends AbstractType<Contract>
 */
class ContractType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $yearList = range(((int)date('Y') - 5), ((int)date('Y') + 5));
        $years = array_combine($yearList, $yearList);
        $builder
            ->add('code', null, [
                'label' => 'Código:',
                'attr' => [
                    'placeholder' => 'Código del contrato'
                ]
            ])
            ->add('year', ChoiceType::class, [
                'label' => 'Año:',
                'placeholder' => '-Seleccione-',
                'choices' => $years,
                'data' => date('Y')
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contract::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}
