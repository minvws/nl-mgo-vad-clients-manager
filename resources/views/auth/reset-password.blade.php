<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}" class="layout-authentication">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $token }}">

        <!-- Email Address -->
        <input type="hidden" name="email" value="{{ $email }}">

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('user.password')" />
            <x-text-input id="password"
                          type="password"
                          name="password"
                          required
                          autocomplete="new-password"
                          aria-describedby="password_errors"
            />
            <x-input-error :messages="$errors->get('password')" id="password_errors" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('user.password_confirm')" />

            <x-text-input id="password_confirmation"
                          type="password"
                          name="password_confirmation"
                          required
                          autocomplete="new-password"
                          aria-describedby="password_confirmation_errors"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" id="password_confirmation_errors" />
        </div>

        <!-- 2FA registration -->
        <div>
            @lang('user.one_time_password.title')
            <a href="{{ $twoFactorQrCodeUrl }}">
                {!! $twoFactorQrCodeSvgWithAria !!}
            </a>

            <x-input-label for="two_factor_code" :value="__('user.one_time_password.code')" />

            <x-text-input id="two_factor_code"
                          type="text"
                          name="two_factor_code"
                          required
                          aria-describedby="two_factor_code_errors"
            />

            <x-input-error :messages="$errors->get('two_factor_code')" id="two_factor_code_errors" />
        </div>

        <div>
            <x-primary-button>
                {{ __('user.reset.button_text') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
