<?php

namespace App\Form;

use App\Entity\Floor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template TData of Floor
 *
 * @extends AbstractType<Floor>
 */
class FloorType extends AbstractType
{
    /**
     * @param FormBuilderInterface<Floor|null> $builder
     * @param array<string, mixed>             $options
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
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
                'novalidate' => 'novalidate',
            ],
            'reply' => false,
        ]);
    }

    /**
     * @param array<mixed> $options
     */
    private function onPreSetData(FormEvent $event, array $options): void
    {
        /** @var Floor $floor */
        $floor = $event->getData();
        $form = $event->getForm();

        $disabled = [];
        if (null !== $floor && null !== $floor->getId() && true === $floor->isGroundFloor()) {
            $disabled = ['disabled' => true, 'readonly' => true];
        }

        $form
            ->add('name', null, [
                'label' => 'Nombre:',
                'attr' => [
                    'placeholder' => 'Nombre de la planta',
                ] + $disabled,
            ]);

        $nextPosition = 0;
        if (!is_null($floor) && is_null($floor->getId()) && !is_null($floor->getBuilding())) {
            $floors = ((bool) $options['reply']) ? $floor->getBuilding()->getReplyExistsFloors() : $floor->getBuilding()->getOriginalExistsFloors();

            if ($floors->count() > 0 && false !== $floors->last()) {
                /** @var int $nextPosition */
                $nextPosition = $floors->last()->getPosition();
            }

            $fieldOptions = [
                'label' => 'Posición:',
                'data' => ($nextPosition + 1),
                'attr' => [
                    'placeholder' => 'Posición',
                ] + $disabled,
            ];
        } else {
            $fieldOptions = [
                'label' => 'Posición:',
                'attr' => [
                    'placeholder' => 'Posición',
                ] + $disabled,
            ];
        }

        $form->add('position', null, $fieldOptions);
    }
}
