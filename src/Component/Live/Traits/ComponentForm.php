<?php

namespace App\Component\Live\Traits;

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
     * @param $eventData
     * @return void
     */
    protected function emitSuccess($eventData): void
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
}