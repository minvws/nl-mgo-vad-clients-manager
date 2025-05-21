@props(['messages'])

@if ($messages)
    <div {{ $attributes->merge(['class' => 'error']) }} role="group" aria-label="foutmelding">
        <span>Foutmelding:</span>
        <ul>
            @foreach ((array) $messages as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif
