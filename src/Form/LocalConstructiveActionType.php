<?php

namespace App\Form;

use App\Entity\ConstructiveAction;
use App\Entity\ConstructiveSystem;
use App\Entity\Local;
use App\Entity\LocalConstructiveAction;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocalConstructiveActionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('constructiveAction', EntityType::class, [
                'class' => ConstructiveAction::class,
                'choice_label' => 'name',
                'label' => 'Tipo:',
                'placeholder' => '-Seleccione-',
                'group_by' => function(ConstructiveAction $constructiveAction, int $key, string $value) {
                    return $constructiveAction->getType()->getLabelFrom($constructiveAction->getType());
                },
            ])
            ->add('constructiveSystem', EntityType::class, [
                'class' => ConstructiveSystem::class,
                'choice_label' => 'name',
                'label' => 'Sistema constructivo:',
                'placeholder' => '-Seleccione-'
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $this->onPreSetData($event);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LocalConstructiveAction::class,
        ]);
    }

    /**
     * @param FormEvent $event
     * @return void
     */
    private function onPreSetData(FormEvent $event): void
    {
//        \Locale::setDefault('en');

        /** @var LocalConstructiveAction $localConstructiveAction */
        $localConstructiveAction = $event->getData();
        $form = $event->getForm();

        $currency = 'CUP';
        $local = null;
        if($localConstructiveAction){
            $local = $localConstructiveAction->getLocal();
            if($local){
                $currency = $local->getSubSystem()->getFloor()->getBuilding()->getProject()->getCurrency()->getCode();
            }
        }

        $form->add('price', MoneyType::class, [
            'label' => 'Precio:',
            'currency' => $currency,
//            'html5' => true,
            'input' => 'integer',
            'divisor' => 100,
            'attr' => [
                'placeholder' => '0',
                'min' => 0,
                'data-usd-currency-target' => 'field',
                'data-controller' => 'money'
            ],
            'data' => (!is_null($local)) ? $local->getPrice() : 0,
            'grouping' => true,
        ]);
    }
}
