<?php

namespace App\Form;

use App\Entity\Building;
use App\Entity\Floor;
use App\Entity\Land;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FloorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $this->onPreSetData($event);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Floor::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ]);
    }

    private function onPreSetData(FormEvent $event): void
    {
        /** @var Floor $floor */
        $floor = $event->getData();
        $form = $event->getForm();

        $disabled = [];
        if (!is_null($floor) && $floor->getId() && $floor->isGroundFloor()) {
            $disabled = ['disabled' => true, 'readonly' => true];
        }

        $form
            ->add('name', null, [
                'label' => 'Nombre:',
                'attr' => [
                    'placeholder' => 'Nombre de la planta'
                ]+$disabled
            ])
            ->add('position', null, [
                'label' => 'Position:',
                'attr' => [
                        'placeholder' => 'Posición'
                    ]+$disabled
            ])
        ;
    }
}
