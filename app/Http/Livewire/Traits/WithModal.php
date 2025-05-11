<?php

namespace App\Http\Livewire\Traits;

trait WithModal
{
    public $showModal = false;
    public $modalType = null;
    public $selectedModel = null;

    public function openModal($type, $modelId = null)
    {
        $this->modalType = $type;
        $this->showModal = true;

        if ($modelId) {
            $this->selectedModel = $this->getModelClass()::find($modelId);
        } else {
            $this->selectedModel = null;
        }

        $this->dispatchBrowserEvent('open-modal', [
            'modal-id' => $this->getModalId()
        ]);
    }

    public function closeModal()
    {
        $this->modalType = null;
        $this->showModal = false;
        $this->selectedModel = null;

        $this->dispatchBrowserEvent('close-modal', [
            'modal-id' => $this->getModalId()
        ]);
    }

    protected function getModalId()
    {
        return match ($this->modalType) {
            'add' => 'add-' . $this->getModelName() . '-modal',
            'edit' => 'edit-' . $this->getModelName() . '-modal',
            'delete' => 'delete-' . $this->getModelName() . '-modal',
            default => null,
        };
    }

    protected function getModelName()
    {
        $className = class_basename($this->getModelClass());
        return strtolower($className);
    }

    abstract protected function getModelClass();
}
