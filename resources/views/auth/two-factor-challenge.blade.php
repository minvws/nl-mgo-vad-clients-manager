<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status :status="session('status')" />



    <form method="post" action="{{ route('two-factor.login') }}" class="layout-authentication">
        @csrf

        <h1>@lang('authentication.one_time_password.title')</h1>

        <p>
            @lang('authentication.one_time_password.description')
        </p>

        <!-- Code -->
        <div>
            <x-input-label for="code" :value="__('authentication.one_time_password.code')"/>
            <x-text-input id="code" type="text" name="code" required autocomplete="one-time-code"/>
            <x-input-error :messages="$errors->get('code')" class="error"/>
        </div>

        <div>
            <x-primary-button>
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
