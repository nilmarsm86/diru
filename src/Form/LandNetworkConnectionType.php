<?php

namespace App\Form;

use App\Entity\Land;
use App\Entity\LandNetworkConnection;
use App\Entity\NetworkConnection;
use App\Form\Types\TechnicalStatusEnumType;
use App\Form\Types\NetworkConnectionEnumType;
use App\Form\Types\UnitMeasurementFloatType;
use App\Form\Types\UnitMeasurementType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LandNetworkConnectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('explanation', null, [
            'label' => 'Explicación:',
        ])
            ->add('networkConnection', EntityType::class, [
                'class' => NetworkConnection::class,
                'choice_label' => 'name',
                'label' => 'Red:',
                'placeholder' => '-Seleccinar-',
            ])
            ->add('type', NetworkConnectionEnumType::class, [
                'label' => 'Tipo de conexión:',
            ])
            ->add('longitude', UnitMeasurementFloatType::class, [
                'label' => 'Longitud:',
                'unit' => 'm',
                'attr' => [
                    'min' => 0
                ]
            ])
            ->add('technicalStatus', TechnicalStatusEnumType::class, [
                'label' => 'Estado técnico:'
            ])
            ->add('landNetworkConnectionConstructiveAction', LandNetworkConnectionConstructiveActionType::class, [
                'required' => true,
                'error_bubbling' => false
            ]);
//        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options): void {
//            $this->onPreSetData($event, $options);
//        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LandNetworkConnection::class,
        ]);
    }

//    /**
//     * @param FormEvent $event
//     * @param array $options
//     * @return void
//     */
//    private function onPreSetData(FormEvent $event, array $options): void
//    {
//        /** @var LandNetworkConnection $landNetworkConnection */
//        $landNetworkConnection = $event->getData();
//        $form = $event->getForm();
//
//
//        $form
//            ->add('explanation', null, [
//                'label' => 'Explicación:',
//            ])
//            ->add('networkConnection', EntityType::class, [
//                'class' => NetworkConnection::class,
//                'choice_label' => 'name',
//                'label' => 'Tipo:',
//                'placeholder' => '-Seleccinar-',
//            ]);
//    }
}
