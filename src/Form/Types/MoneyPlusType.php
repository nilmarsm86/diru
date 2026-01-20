<?php

namespace App\Form\Types;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template TData of array
 *
 * @extends AbstractType<array>
 */
class MoneyPlusType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('add', false)
            ->setDefault('add_title', 'Agregar')
            ->setDefault('add_id', '')
            ->setDefault('add_placeholder', 'Cargando...')
            ->setDefault('add_url', '')
            ->setDefault('add_icon', 'bi:plus-lg')
            ->setDefault('list', false)
            ->setDefault('list_title', 'Detalle')
            ->setDefault('list_id', '')
            ->setDefault('list_placeholder', 'Cargando...')
            ->setDefault('list_url', '')
            ->setDefault('list_icon', 'bi:list')
        ;

        $resolver->setAllowedTypes('add', 'bool');
        $resolver->setAllowedTypes('add_title', 'string');
        $resolver->setAllowedTypes('add_id', 'string');
        $resolver->setAllowedTypes('add_placeholder', 'string');
        $resolver->setAllowedTypes('add_url', 'string');
        $resolver->setAllowedTypes('add_icon', 'string');

        $resolver->setAllowedTypes('list', 'bool');
        $resolver->setAllowedTypes('list_title', 'string');
        $resolver->setAllowedTypes('list_id', 'string');
        $resolver->setAllowedTypes('list_placeholder', 'string');
        $resolver->setAllowedTypes('list_url', 'string');
        $resolver->setAllowedTypes('list_icon', 'string');
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['add'] = $options['add'];
        $view->vars['add_title'] = $options['add_title'];
        $view->vars['add_id'] = $options['add_id'];
        $view->vars['add_placeholder'] = $options['add_placeholder'];
        $view->vars['add_url'] = $options['add_url'];
        $view->vars['add_icon'] = $options['add_icon'];

        $view->vars['list'] = $options['list'];
        $view->vars['list_title'] = $options['list_title'];
        $view->vars['list_id'] = $options['list_id'];
        $view->vars['list_placeholder'] = $options['list_placeholder'];
        $view->vars['list_url'] = $options['list_url'];
        $view->vars['list_icon'] = $options['list_icon'];
    }

    public function getParent(): string
    {
        return MoneyType::class;
    }
}
