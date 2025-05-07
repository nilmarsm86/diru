<?php

namespace App\Form;

use App\Entity\CorporateEntity;
use App\Entity\Municipality;
use App\Entity\Organism;
use App\Form\Types\AddressType;
use App\Form\Types\CorporateEntityTypeEnumType;
use App\Form\Types\EntityPlusType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CorporateEntityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nombre:'
            ])
            ->add('code', null, [
                'label' => 'Código de empresa:'
            ])
            ->add('nit', null, [
                'label' => 'NIT:',
                'help' => 'Número de Identificación Tributaria'
            ])
            ->add('type', CorporateEntityTypeEnumType::class, [
                'label' => 'Tipo de entidad:',
            ])
            ->add('address', AddressType::class, [
                'province' => $options['province'],
                'municipality' => $options['municipality'],
                'mapped' => false,
                'live_form' => $options['live_form'],
                'modal' => $options['modal']
            ]);

        $organismAttr = [
            'class' => Organism::class,
            'choice_label' => 'name',
            'label' => 'Organismo:',
            'placeholder' => '-Seleccione-',
            'attr' => [
//                    'data-model' => 'norender|organism',
            ]
        ];

        if (is_null($options['modal'])) {
            $builder
                ->add('organism', EntityPlusType::class, [
                    'modal_id' => '#add-organism',
                    'path' => 'app_organism_options'
                ]+$organismAttr);
        } else {
            $builder->add('organism', EntityType::class, []+$organismAttr);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CorporateEntity::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ],
            'province' => 0,
            'municipality' => 0,
            'error_mapping' => [
                'enumType' => 'type',
            ],
            'live_form' => false,
            'modal' => null
        ]);

        $resolver->setAllowedTypes('province', 'int');
        $resolver->setAllowedTypes('municipality', 'int');
        $resolver->setAllowedTypes('live_form', 'bool');
        $resolver->setAllowedTypes('modal', ['null', 'string']);
    }
}
