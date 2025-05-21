<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="layout-authentication">
        @csrf
        <fieldset>
            <legend class="visually-hidden">{{ __('authentication.data') }}</legend>
            <x-input-error-manon :messages="$errors->get('authentication')" class="error"/>

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('user.email')"/>
                <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username"/>
                <x-input-error :messages="$errors->get('email')" class="error"/>
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('user.password')"/>
                <x-text-input id="password" type="password" name="password" required autocomplete="current-password"/>
                <x-input-error :messages="$errors->get('password')" class="error"/>
            </div>
        </fieldset>

        <div>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">
                    {{ __('authentication.forgot_password.title') }}
                </a>
            @endif

            <x-primary-button>
                {{ __('authentication.login') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
