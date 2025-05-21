@props(['disabled' => false, 'hasError' => false])
<textarea
    {{ $disabled ? 'disabled' : '' }}
    {!! $attributes->merge(['class' => 'form-control']) !!}
    @if($hasError) aria-invalid="true"@endif>{{ $slot }}</textarea>