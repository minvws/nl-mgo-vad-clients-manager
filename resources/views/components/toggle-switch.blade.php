@props(['name', 'id' => null, 'checked' => false, 'value' => '1'])

<label class="switch">
    <input type="hidden" name="{{ $name }}" value="0">
    <input 
        type="checkbox"
        id="{{ $id ?? $name }}"
        name="{{ $name }}"
        value="{{ $value }}"
        {{ $checked ? 'checked' : '' }}
        {{ $attributes }}
    >
    <span class="slider round"></span>
</label>