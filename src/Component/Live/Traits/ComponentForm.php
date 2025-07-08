<?php

namespace App\Component\Live\Traits;

use App\Component\Twig\Modal\Modal;

trait ComponentForm
{
    /**
     * @return bool
     */
    public function hasValidationErrors(): bool
    {
        return $this->getForm()->isSubmitted() && !$this->getForm()->isValid();
    }

//    abstract protected function getSuccessFormEventName(): string;

    /**
     * Get form success event name
     * @return string
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
     * Emit success event for all
     * @param array $eventData
     * @return void
     */
    protected function emitSuccess(array $eventData): void
    {
        $this->dispatchBrowserEvent($this->getSuccessFormEventName(), $eventData);
        $this->resetForm();
    }

    /**
     * @param $classname
     * @return false|int|string
     */
    protected function getClassName($classname): false|int|string
    {
        if ($pos = strrpos($classname, '\\')) return substr($classname, $pos + 1);
        return $pos;
    }

    /**
     * @param object $entity
     * @param string $message
     * @param string $type
     * @return string
     */
    private function getSuccessTemplate(object $entity, string $message, string $type='text-bg-success'): string
    {
        return $this->renderView("partials/_form_success.html.twig", [
            'id' => 'new_' . $this->getClassName($entity::class) . '_' . $entity->getId().'_'.time(),
            'type' => $type,
            'message' => $message
        ]);
    }

    /**
     * @param object $entity
     * @param string $message
     * @param array $updateEventData
     * @param string $mssageType
     * @return void
     */
    public function modalManage(object $entity, string $message, array $updateEventData, string $mssageType = 'text-bg-primary'): void
    {
        $template = $this->getSuccessTemplate($entity, $message, $mssageType);

        $eventData = [
            'response' => $template,
            'modal' => $this->modal,
            'data' => $updateEventData
        ];
        $this->dispatchBrowserEvent('type--entity-plus:update', $eventData);
        $this->dispatchBrowserEvent(Modal::MODAL_CLOSE);

        $this->resetForm();
    }

    /**
     * @param object $entity
     * @param string $message
     * @return void
     */
    public function ajaxManage(object $entity, string $message = ''): void
    {
        $template = $this->getSuccessTemplate($entity, $message);

        $this->dispatchBrowserEvent($this->getSuccessFormEventName(), [
            'response' => $template
        ]);

        $this->resetForm();
    }
}