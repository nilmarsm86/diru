<?php

namespace App\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

final class TogglePasswordTypeExtension extends AbstractTypeExtension
{
    public function __construct(private readonly ?TranslatorInterface $translator)
    {
    }

    public static function getExtendedTypes(): iterable
    {
        return [PasswordType::class];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'toggle' => false,
            'hidden_label' => 'Hide',
            'visible_label' => 'Show',
            'hidden_icon' => 'Default',
            'visible_icon' => 'Default',
            'button_classes' => ['toggle-password-button'],
            'toggle_container_classes' => ['toggle-password-container'],
            'toggle_translation_domain' => null,
            'use_toggle_form_theme' => true,
        ]);

        $resolver->setNormalizer(
            'toggle_translation_domain',
            static fn (Options $options, $labelTranslationDomain) => $labelTranslationDomain ?? $options['translation_domain'],
        );

        $resolver->setAllowedTypes('toggle', ['bool']);
        $resolver->setAllowedTypes('hidden_label', ['string', TranslatableMessage::class, 'null']);
        $resolver->setAllowedTypes('visible_label', ['string', TranslatableMessage::class, 'null']);
        $resolver->setAllowedTypes('hidden_icon', ['string', 'null']);
        $resolver->setAllowedTypes('visible_icon', ['string', 'null']);
        $resolver->setAllowedTypes('button_classes', ['string[]']);
        $resolver->setAllowedTypes('toggle_container_classes', ['string[]']);
        $resolver->setAllowedTypes('toggle_translation_domain', ['string', 'bool', 'null']);
        $resolver->setAllowedTypes('use_toggle_form_theme', ['bool']);
    }

    /**
     * @throws \JsonException
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['toggle'] = $options['toggle'];

        if (false === (bool) $options['toggle']) {
            return;
        }

        if (true === (bool) $options['use_toggle_form_theme'] && is_array($view->vars['block_prefixes'])) {
            array_splice($view->vars['block_prefixes'], -1, 0, 'toggle_password');
        }

        $controllerName = 'toggle-password';
        $attr = is_array($view->vars['attr']) ? $view->vars['attr'] : [];
        /** @var string $dataController */
        $dataController = $attr['data-controller'] ?? '';
        $attr['data-controller'] = trim(\sprintf('%s %s', $dataController, $controllerName));
        $view->vars['attr'] = $attr;

        $controllerValues = [];
        if (false !== (bool) $options['toggle_translation_domain']) {
            /** @var string $hiddenLabel */
            $hiddenLabel = $options['hidden_label'];
            /** @var string $toggleTranslationDomain */
            $toggleTranslationDomain = $options['toggle_translation_domain'];
            /** @var string $visibleLabel */
            $visibleLabel = $options['visible_label'];
            $controllerValues['hidden-label'] = $this->translateLabel($hiddenLabel, $toggleTranslationDomain);
            $controllerValues['visible-label'] = $this->translateLabel($visibleLabel, $toggleTranslationDomain);
        } else {
            $controllerValues['hidden-label'] = $options['hidden_label'];
            $controllerValues['visible-label'] = $options['visible_label'];
        }

        $controllerValues['hidden-icon'] = $options['hidden_icon'];
        $controllerValues['visible-icon'] = $options['visible_icon'];
        $controllerValues['button-classes'] = json_encode($options['button_classes'], \JSON_THROW_ON_ERROR);

        foreach ($controllerValues as $name => $value) {
            $view->vars['attr'][\sprintf('data-%s-%s-value', $controllerName, $name)] = $value;
        }

        $view->vars['toggle_container_classes'] = $options['toggle_container_classes'];
    }

    private function translateLabel(string|TranslatableMessage|null $label, ?string $translationDomain): ?string
    {
        if (null === $this->translator || null === $label) {
            return $label;
        }

        if ($label instanceof TranslatableMessage) {
            return $label->trans($this->translator);
        }

        return $this->translator->trans($label, domain: $translationDomain);
    }
}
