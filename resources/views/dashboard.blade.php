<x-app-layout>
    <x-slot name="heading">
        <h1>{{ __('Dashboard') }}</h1>
    </x-slot>

    <div>
        <div>
            <div>
                <div>
                    {{ __("You're logged in!") }}
                </div>

                @if (session('status') == 'two-factor-authentication-enabled')
                    <div>
                        Please finish configuring two factor authentication below.
                    </div>
                @elseif (session('status') == 'two-factor-authentication-disabled')
                    <div>
                        Two factor authentication has been disabled.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
