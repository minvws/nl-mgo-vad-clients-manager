<x-guest-layout>
    <form method="post" action="{{ route('password.email') }}" class="layout-authentication">
        @csrf

        <h1>{{ __('authentication.forgot_password.title') }}</h1>

        <p>
            @lang('authentication.forgot_password.description')
        </p>
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('user.email')"/>
            <x-text-input id="email" type="email" name="email" :value="old('email')" required/>
            <x-input-error :messages="$errors->get('email')"/>
        </div>

        <div>
            <x-primary-button>
                {{ __('general.send') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
