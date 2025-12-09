<?php

namespace App\Form;

use App\Entity\SubSystem;
use App\Entity\SubsystemSubType;
use App\Entity\SubsystemTypeSubsystemSubType;
use App\Form\Types\SubSystemClassificationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubSystemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nombre:',
                'attr' => [
                    'placeholder' => 'Nombre del subsistema'
                ]
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options): void {
            $this->onPreSetData($event, $options);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SubSystem::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ],
            'type' => 0,
            'subType' => 0,
            'live_form' => false,
            'modal' => null,
        ]);

        $resolver->setAllowedTypes('type', 'int');
        $resolver->setAllowedTypes('subType', 'int');
        $resolver->setAllowedTypes('live_form', 'bool');
        $resolver->setAllowedTypes('modal', ['null', 'string']);
    }

    /**
     * @param FormEvent $event
     * @param array<mixed> $options
     * @return void
     */
    private function onPreSetData(FormEvent $event, array $options): void
    {
        /** @var SubSystem $subSystem */
        $subSystem = $event->getData();
        $form = $event->getForm();

        $type = 0;
        $subType = 0;
        if ($subSystem && $subSystem->getId()) {
            $subsystemTypeSubsystemSubType = $subSystem->getSubsystemTypeSubsystemSubType();
            $subsystemType = $subsystemTypeSubsystemSubType?->getSubsystemType();
            $type = $subsystemType?->getId();
            $subsystemSubType = $subsystemTypeSubsystemSubType?->getSubsystemSubType();
            $subType = $subsystemSubType?->getId();
        }

        $form->add('subsystemClassification', SubSystemClassificationType::class, [
            'mapped' => false,
            'type' => $type,
            'subType' => $subType,
            'live_form' => $options['live_form'],
            'modal' => $options['modal']
        ]);
    }
}
