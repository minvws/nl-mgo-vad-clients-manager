@props([
    'name',
    'options' => [],
    'selected' => null,
    'placeholder' => '',
    'searchPlaceholder' => '',
    'required' => false
])

<div id="searchable-select-{{ $name }}" class="searchable-select" data-name="{{ $name }}">
    <input type="hidden"
           name="{{ $name }}"
           value="{{ $selected }}"
           data-label="{{ collect($options)->get($selected) }}"
           {{ $required ? 'required' : '' }}>
    <div class="searchable-select-wrapper">
        <input type="text"
               class="searchable-select-input"
               value="{{ collect($options)->get($selected) }}"
               placeholder="{{ $placeholder }}"
               readonly>
        <div class="searchable-select-indicator">
            <svg class="searchable-select-chevron" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </div>
    </div>

    <div class="searchable-select-dropdown">
        <div class="searchable-select-search">
            <input type="text"
                   class="searchable-select-search-input"
                   placeholder="{{ $searchPlaceholder }}">
        </div>
        <div class="searchable-select-options">
            @foreach($options as $value => $label)
                <div class="searchable-select-option {{ $selected === $value ? 'selected' : '' }}"
                     data-value="{{ $value }}"
                     data-label="{{ $label }}"
                     tabindex="0">
                    {{ $label }}
                </div>
            @endforeach
        </div>
    </div>
</div>

