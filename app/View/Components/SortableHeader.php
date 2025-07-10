<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

use function route;
use function view;

class SortableHeader extends Component
{
    public string $url;
    public string $currentDirection;
    public string $newDirection;

    public function __construct(
        public string $route,
        public string $sort,
        public string $direction,
        public string $sortField,
        public string $label,
        public string $search = '',
        public ?bool $active = null,
    ) {
        $this->currentDirection = $sort === $sortField ? $direction : 'asc';
        $this->newDirection = $this->currentDirection === 'asc' ? 'desc' : 'asc';
        $this->url = route($route, [
            'sort' => $sortField,
            'direction' => $this->newDirection,
            'search' => $search,
            'active' => $active,
        ]);
    }

    public function render(): View
    {
        return view('components.sortable-header');
    }
}
