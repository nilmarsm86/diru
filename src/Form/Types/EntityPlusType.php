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

            ->setDefault('placeholder', '-Seleccione-')

            ->setDefault('add', false)
            ->setDefault('add_title', 'Agregar')
            ->setDefault('add_id', '')
            ->setDefault('add_placeholder', 'Cargando...')
            ->setDefault('add_url', '')
            ->setDefault('add_icon', 'bi:plus-lg')

            ->setDefault('detail', false)
            ->setDefault('detail_title', 'Detalle')
            ->setDefault('detail_id', '')
            ->setDefault('detail_placeholder', 'Cargando...')
            ->setDefault('detail_url', '')
            ->setDefault('detail_icon', 'bi:eye')

            ->setDefault('modify', false)
            ->setDefault('modify_title', 'Modificar')
            ->setDefault('modify_id', '')
            ->setDefault('modify_placeholder', 'Cargando...')
            ->setDefault('modify_url', '')
            ->setDefault('modify_icon', 'fa:edit')
        ;

        $resolver->setAllowedTypes('add', 'bool');
        $resolver->setAllowedTypes('add_title', 'string');
        $resolver->setAllowedTypes('add_id', 'string');
        $resolver->setAllowedTypes('add_placeholder', 'string');
        $resolver->setAllowedTypes('add_url', 'string');
        $resolver->setAllowedTypes('add_icon', 'string');

        $resolver->setAllowedTypes('detail', 'bool');
        $resolver->setAllowedTypes('detail_title', 'string');
        $resolver->setAllowedTypes('detail_id', 'string');
        $resolver->setAllowedTypes('detail_placeholder', 'string');
        $resolver->setAllowedTypes('detail_url', 'string');
        $resolver->setAllowedTypes('detail_icon', 'string');

        $resolver->setAllowedTypes('modify', 'bool');
        $resolver->setAllowedTypes('modify_title', 'string');
        $resolver->setAllowedTypes('modify_id', 'string');
        $resolver->setAllowedTypes('modify_placeholder', 'string');
        $resolver->setAllowedTypes('modify_url', 'string');
        $resolver->setAllowedTypes('modify_icon', 'string');
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['add'] = $options['add'];
        $view->vars['add_title'] = $options['add_title'];
        $view->vars['add_id'] = $options['add_id'];
        $view->vars['add_placeholder'] = $options['add_placeholder'];
        $view->vars['add_url'] = $options['add_url'];
        $view->vars['add_icon'] = $options['add_icon'];

        $view->vars['detail'] = $options['detail'];
        $view->vars['detail_title'] = $options['detail_title'];
        $view->vars['detail_id'] = $options['detail_id'];
        $view->vars['detail_placeholder'] = $options['detail_placeholder'];
        $view->vars['detail_url'] = $options['detail_url'];
        $view->vars['detail_icon'] = $options['detail_icon'];

        $view->vars['modify'] = $options['modify'];
        $view->vars['modify_title'] = $options['modify_title'];
        $view->vars['modify_id'] = $options['modify_id'];
        $view->vars['modify_placeholder'] = $options['modify_placeholder'];
        $view->vars['modify_url'] = $options['modify_url'];
        $view->vars['modify_icon'] = $options['modify_icon'];
    }
    public function getParent(): string
    {
        return EntityType::class;
    }
}
