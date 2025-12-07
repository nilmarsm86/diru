<?php

namespace App\Twig\Runtime;

use Symfony\UX\StimulusBundle\Dto\StimulusAttributes;
use Symfony\UX\StimulusBundle\Helper\StimulusHelper;
use Twig\Extension\RuntimeExtensionInterface;

readonly class StimulusControllerTwigExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(private StimulusHelper $stimulusHelper)
    {
        // Inject dependencies if needed
    }

    /**
     * @param string $controllerName the Stimulus controller name
     * @param array<mixed> $controllerValues array of controller values
     * @param array<mixed> $controllerClasses array of controller CSS classes
     * @param array<mixed> $controllerOutlets array of controller outlets
     * @return StimulusAttributes
     */
    public function renderStimulusController(string $controllerName, array $controllerValues = [], array $controllerClasses = [], array $controllerOutlets = []): StimulusAttributes
    {
        $controllerName = strtr($controllerName, ['/' => '--']);
        $stimulusAttributes = $this->stimulusHelper->createStimulusAttributes();
        $stimulusAttributes->addController($controllerName, $controllerValues, $controllerClasses, $controllerOutlets);

        return $stimulusAttributes;
    }

    /**
     * @param string $controllerName
     * @param string|null $actionName
     * @param string|null $eventName
     * @param array<mixed> $parameters
     * @return StimulusAttributes
     */
    public function renderStimulusAction(string $controllerName, ?string $actionName = null, ?string $eventName = null, array $parameters = []): StimulusAttributes
    {
        $controllerName = strtr($controllerName, ['/' => '--']);
        $stimulusAttributes = $this->stimulusHelper->createStimulusAttributes();
        $stimulusAttributes->addAction($controllerName, $actionName, $eventName, $parameters);

        return $stimulusAttributes;
    }

    /**
     * @param string $controllerName the Stimulus controller name
     * @param string|null $targetNames The space-separated list of target names if a string is passed to the 1st argument. Optional.
     * @return StimulusAttributes
     */
    public function renderStimulusTarget(string $controllerName, ?string $targetNames = null): StimulusAttributes
    {
        $controllerName = strtr($controllerName, ['/' => '--']);
        $stimulusAttributes = $this->stimulusHelper->createStimulusAttributes();
        $stimulusAttributes->addTarget($controllerName, $targetNames);

        return $stimulusAttributes;
    }
}
