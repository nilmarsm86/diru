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
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options): void {
            $this->onPreSetData($event, $options);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Floor::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ],
            'reply' => false,
        ]);
    }

    private function onPreSetData(FormEvent $event, array $options): void
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
                    ] + $disabled
            ]);

        $nextPosition = 0;
        if (!is_null($floor) && is_null($floor->getId()) && !is_null($floor->getBuilding())) {
            if ($options['reply']) {
                $nextPosition = $floor->getBuilding()->getReplyFloors()->count();
            } else {
                $nextPosition = $floor->getBuilding()->getOriginalFloors()->count();
            }

            $fieldOptions = [
                'label' => 'Posición:',
                'data' => ($nextPosition + 1),
                'attr' => [
                        'placeholder' => 'Posición'
                    ] + $disabled
            ];
        }else{
            $fieldOptions = [
                'label' => 'Posición:',
                'attr' => [
                        'placeholder' => 'Posición'
                    ] + $disabled
            ];
        }

        $form->add('position', null, $fieldOptions);
    }
}
