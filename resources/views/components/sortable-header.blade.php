@props([
    'route',
    'sort',
    'direction',
    'search' => '',
    'active' => '',
    'sortField',
    'label'
])

<th scope="col">
    <a href="{{ $url }}" class="sortable {{ $sort === $sortField ? 'sorted-' . $direction : '' }}">
        <span>{{ $label }}</span>
        @if ($sort === $sortField && $direction === 'asc')
            <x-tabler-sort-ascending/>
        @endif
        @if ($sort === $sortField && $direction === 'desc')
            <x-tabler-sort-descending/>
        @endif
    </a>
</th>
