@props([
    'route',
    'sort',
    'direction',
    'search',
    'sortField',
    'label'
])

<th scope="col">
    <a href="{{ route($route, ['sort' => $sortField, 'direction' => $sort === $sortField && $direction === 'asc' ? 'desc' : 'asc', 'search' => $search]) }}" class="sortable">
        <span>{{ $label }}</span>
        @if ($sort === $sortField && $direction === 'asc')
            <x-tabler-sort-ascending/>
        @endif
        @if ($sort === $sortField && $direction === 'desc')
            <x-tabler-sort-descending/>
        @endif
    </a>
</th>
