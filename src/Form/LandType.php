<?php

namespace App\Form;

use App\Entity\Land;
use App\Form\Types\UnitMeasurementType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

class LandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('landArea', UnitMeasurementType::class, [
                'unit' => 'm<sup>2</sup>',
                'label' => "Area de terreno:"
            ])
            ->add('occupiedArea', UnitMeasurementType::class, [
                'unit' => 'm<sup>2</sup>',
                'label' => "Area ocupada:"
            ])
            ->add('perimeter', UnitMeasurementType::class, [
                'unit' => 'm',
                'label' => "Perímetro:"
            ])
            ->add('photo', FileType::class, [
                'label' => "Foto:",
            ])
            ->add('microlocalization', FileType::class, [
                'label' => "Microlocalización:",
            ])
            ->add('floor', NumberType::class, [
                'label' => "Plantas:"
            ])
            ->add('landNetworkConnections', LiveCollectionType::class, [
                'entry_type' => LandNetworkConnectionType::class,
                'button_delete_options' => [
                    'label_html' => true
                ],
                'error_bubbling' => false,
            ])
            ->add('isOccupied', ChoiceType::class, [
                'label' => 'Area ocupada:',
                'mapped' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    '&nbsp;' => '1'
                ],
                'label_html' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Land::class,
        ]);
    }
}
