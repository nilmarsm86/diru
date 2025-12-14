<?php

namespace App\Component\Live\Traits;

use App\Component\Twig\Modal\Modal;

trait ComponentForm
{
    public function hasValidationErrors(): bool
    {
        return $this->getForm()->isSubmitted() && !$this->getForm()->isValid();
    }

    //    abstract protected function getSuccessFormEventName(): string;

    /**
     * Get form success event name.
     */
    protected function getSuccessFormEventName(): string
    {
        return ':form_success';
    }

    protected function isSubmitAndValid(): bool
    {
        return $this->getForm()->isSubmitted() && $this->getForm()->isValid();
    }

    /**
     * Emit success event for all.
     *
     * @param array<mixed> $eventData
     */
    protected function emitSuccess(array $eventData): void
    {
        $this->dispatchBrowserEvent($this->getSuccessFormEventName(), $eventData);
        $this->resetForm();
    }

    private function getClassName(string $classname): string
    {
        $pos = strrpos($classname, '\\');

        if (false !== $pos) {
            return substr($classname, $pos + 1);
        }

        return $classname;
    }

    private function getSuccessTemplate(object $entity, string $message, string $type = 'text-bg-success'): string
    {
        $callback = [$entity, 'getId'];
        assert(is_callable($callback));

        /** @var string $callbackResult */
        $callbackResult = call_user_func_array($callback, []);

        return $this->renderView('partials/_form_success.html.twig', [
            'id' => 'new_'.$this->getClassName($entity::class).'_'.$callbackResult.'_'.time(),
            'type' => $type,
            'message' => $message,
        ]);
    }

    /**
     * @param array<mixed> $updateEventData
     */
    public function modalManage(object $entity, string $message, array $updateEventData, string $mssageType = 'text-bg-primary'): void
    {
        $template = $this->getSuccessTemplate($entity, $message, $mssageType);

        $eventData = [
            'response' => $template,
            'modal' => $this->modal,
            'data' => $updateEventData,
        ];
        $this->dispatchBrowserEvent('type--entity-plus:update', $eventData);
        $this->dispatchBrowserEvent(Modal::MODAL_CLOSE);

        $this->resetForm();
    }

    public function ajaxManage(object $entity, string $message = ''): void
    {
        $template = $this->getSuccessTemplate($entity, $message);

        $this->dispatchBrowserEvent($this->getSuccessFormEventName(), [
            'response' => $template,
        ]);

        $this->resetForm();
    }
}
