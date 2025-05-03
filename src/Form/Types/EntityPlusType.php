<?php

namespace App\Form\Types;

use App\Entity\Enums\CorporateEntityType;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntityPlusType extends AbstractType
{

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('icon', 'bi:plus-lg')
            ->setDefault('modal_id', '')
            ->setDefault('path', '')
        ;

        $resolver->setAllowedTypes('icon', 'string');
        $resolver->setAllowedTypes('modal_id', 'string');
        $resolver->setAllowedTypes('path', 'string');
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['icon'] = $options['icon'];
        $view->vars['modal_id'] = $options['modal_id'];
        $view->vars['path'] = $options['path'];
    }
    public function getParent(): string
    {
        return EntityType::class;
    }
}
