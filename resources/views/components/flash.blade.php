@php /** @var \App\Components\FlashNotification $flash */ @endphp
<section {{ $attributes->merge(['class' => $cssStyles]) }} role="group" aria-label="{{ $ariaLabel }}">
    <div>
        <span>{{ $header }}</span>
        {{ __($flash->getMessage()) }}

        @if ($flash->hasAdditionalMessages())
            <ul>
                @foreach ($flash->getAdditionalMessages() as $message)
                    <li>{{ __($message) }}</li>
                @endforeach
            </ul>
        @endif
    </div>
</section>
