<?php

namespace App\Form\Types;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MoneyPlusType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver

//            ->setDefault('placeholder', '-Seleccione-')

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

//            ->setDefault('modify', false)
//            ->setDefault('modify_title', 'Modificar')
//            ->setDefault('modify_id', '')
//            ->setDefault('modify_placeholder', 'Cargando...')
//            ->setDefault('modify_url', '')
//            ->setDefault('modify_icon', 'fa:edit')
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

        //        $resolver->setAllowedTypes('modify', 'bool');
        //        $resolver->setAllowedTypes('modify_title', 'string');
        //        $resolver->setAllowedTypes('modify_id', 'string');
        //        $resolver->setAllowedTypes('modify_placeholder', 'string');
        //        $resolver->setAllowedTypes('modify_url', 'string');
        //        $resolver->setAllowedTypes('modify_icon', 'string');
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

        //        $view->vars['modify'] = $options['modify'];
        //        $view->vars['modify_title'] = $options['modify_title'];
        //        $view->vars['modify_id'] = $options['modify_id'];
        //        $view->vars['modify_placeholder'] = $options['modify_placeholder'];
        //        $view->vars['modify_url'] = $options['modify_url'];
        //        $view->vars['modify_icon'] = $options['modify_icon'];
    }

    public function getParent(): string
    {
        return MoneyType::class;
    }
}
