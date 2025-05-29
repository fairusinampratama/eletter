<?php

namespace App\View\Components\Dashboard;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PageWrapper extends Component
{
    public $title;
    public $breadcrumbItems;

    public function __construct($title, $breadcrumbItems = [])
    {
        $this->title = $title;
        $this->breadcrumbItems = $breadcrumbItems;
    }

    public function render(): View|Closure|string
    {
        return view('components.dashboard.page-wrapper');
    }
}
