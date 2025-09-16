<?php

namespace App\Form;

use App\Entity\ConstructiveAction;
use App\Entity\ConstructiveSystem;
use App\Entity\LandNetworkConnectionConstructiveAction;
use App\Entity\Local;
use App\Entity\LocalConstructiveAction;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LandNetworkConnectionConstructiveActionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('constructiveAction', EntityType::class, [
                'class' => ConstructiveAction::class,
                'choice_label' => 'name',
                'label' => 'Tipo:',
                'placeholder' => '-Seleccione-'
            ])
            ->add('constructiveSystem', EntityType::class, [
                'class' => ConstructiveSystem::class,
                'choice_label' => 'name',
                'label' => 'Sistema constructivo:',
                'placeholder' => '-Seleccione-'
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options): void {
            $this->onPreSetData($event, $options);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LandNetworkConnectionConstructiveAction::class,
        ]);
    }

    /**
     * @param FormEvent $event
     * @return void
     */
    private function onPreSetData(FormEvent $event): void
    {
        /** @var LandNetworkConnectionConstructiveAction $landNetworkConnectionConstructiveAction */
        $landNetworkConnectionConstructiveAction = $event->getData();
        $form = $event->getForm();

        $currency = 'CUP';
        if($landNetworkConnectionConstructiveAction){
            $landNetworkConnection = $landNetworkConnectionConstructiveAction->getLandNetworkConnection();
            if($landNetworkConnection){
                $currency = $landNetworkConnection->getBuilding()->getProject()->getCurrency()->getCode();
            }
        }

        $form->add('price', MoneyType::class, [
            'label' => 'Precio:',
            'currency' => $currency,
            'html5' => true,
            'input' => 'integer',
            'divisor' => 100,
            'attr' => [
                'placeholder' => 0,
                'min' => 0,
                'data-usd-currency-target' => 'field'
            ],
            'empty_data' => 0
        ]);
    }
}
