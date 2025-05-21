<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Support\Config;
use Illuminate\View\Component;
use Illuminate\View\View;

use function view;

class AppLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app', ['appName' => Config::string('app.name')]);
    }
}
