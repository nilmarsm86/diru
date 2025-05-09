<?php

namespace App\Form\Types;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntityPlusType extends AbstractType
{

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('icon', 'bi:plus-lg')
            ->setDefault('modal_id', '')
            ->setDefault('path', '')
            ->setDefault('placeholder', '-Seleccionar-')
            ->setDefault('detail', false)
            ->setDefault('detail_title', 'Detalle')
            ->setDefault('detail_id', '')
            ->setDefault('detail_loading', '')
            ->setDefault('detail_url', '')
            ->setDefault('detail_icon', 'bi:eye')
        ;

        $resolver->setAllowedTypes('icon', 'string');
        $resolver->setAllowedTypes('modal_id', 'string');
        $resolver->setAllowedTypes('path', 'string');
        $resolver->setAllowedTypes('detail', 'bool');
        $resolver->setAllowedTypes('detail_title', 'string');
        $resolver->setAllowedTypes('detail_id', 'string');
        $resolver->setAllowedTypes('detail_loading', 'string');
        $resolver->setAllowedTypes('detail_url', 'string');
        $resolver->setAllowedTypes('detail_icon', 'string');
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['icon'] = $options['icon'];
        $view->vars['modal_id'] = $options['modal_id'];
        $view->vars['path'] = $options['path'];
        $view->vars['detail'] = $options['detail'];
        $view->vars['detail_title'] = $options['detail_title'];
        $view->vars['detail_id'] = $options['detail_id'];
        $view->vars['detail_loading'] = $options['detail_loading'];
        $view->vars['detail_url'] = $options['detail_url'];
        $view->vars['detail_icon'] = $options['detail_icon'];
    }
    public function getParent(): string
    {
        return EntityType::class;
    }
}
