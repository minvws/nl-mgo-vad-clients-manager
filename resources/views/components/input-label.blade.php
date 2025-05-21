@props(['value', 'help_text'])

<label {{ $attributes->merge(['class' => 'form-label']) }}>
    {{ $value ?? $slot }}
    @if($help_text ?? false)
        <span class="form-text">{{ $help_text }}</span>
    @endif
</label>
