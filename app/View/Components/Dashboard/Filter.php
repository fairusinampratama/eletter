<?php

namespace App\View\Components\Dashboard;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Filter extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $route,
        public string $placeholder = 'Search...',
        public ?string $buttonText = 'Add new',
        public string $searchId = 'search-input',
        public bool $showAddButton = true
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.dashboard.filter');
    }
}
