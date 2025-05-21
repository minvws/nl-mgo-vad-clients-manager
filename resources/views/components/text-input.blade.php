@props(['disabled' => false, 'hasError' => false])

<input
    {{ $disabled ? 'disabled' : '' }}
    {!! $attributes->merge(['class' => 'form-control']) !!}
    @if($hasError) aria-invalid="true"@endif />
