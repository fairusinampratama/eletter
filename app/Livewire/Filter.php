<?php

namespace App\Livewire;

use Livewire\Component;

class Filter extends Component
{
    public $search = '';
    public $route;
    public $placeholder;
    public $buttonText;
    public $showAddButton;

    public function mount($route, $placeholder = 'Search...', $buttonText = 'Add new', $showAddButton = true)
    {
        $this->route = $route;
        $this->placeholder = $placeholder;
        $this->buttonText = $buttonText;
        $this->showAddButton = $showAddButton;
    }

    public function displayAddModal()
    {
        $this->dispatch('displayAddModal');
    }

    public function updatingSearch()
    {
        $this->dispatch('search-updated', search: $this->search);
    }

    public function render()
    {
        return view('livewire.filter');
    }
}
