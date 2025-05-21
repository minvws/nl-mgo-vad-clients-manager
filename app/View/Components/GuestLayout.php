<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Support\Config;
use Illuminate\View\Component;
use Illuminate\View\View;

use function view;

class GuestLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.guest', ['appName' => Config::string('app.name')]);
    }
}
