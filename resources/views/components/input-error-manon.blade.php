@props(['messages'])

@if ($messages)
    <div {{ $attributes }} role="group" aria-label="foutmelding">
        <ul>
            @foreach ((array) $messages as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif
