<?php

namespace App\View\Components\Dashboard;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Sidebar extends Component
{
    public $menuItems;

    public function __construct($menuItems = [])
    {
        $this->menuItems = $this->markActiveItems($menuItems);
    }

    protected function markActiveItems($items)
    {
        return array_map(function ($item) {
            $item['active'] = $item['active'] ?? request()->url() === ($item['url'] ?? '#');
            return $item;
        }, $items);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.dashboard.sidebar');
    }
}
