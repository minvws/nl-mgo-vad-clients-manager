<x-guest-layout>

    <section class="layout-authentication">
        <div class="visually-grouped">
            <form method="post" action="{{ route('register') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <fieldset>
                    <legend>{{ __('user.register.title') }}</legend>
                    <p>{{ __('user.register.text') }}</p>
                    <!-- Name -->
                    <div>
                        <x-input-label for="name" :value="__('user.name')" />
                        <x-text-input id="name" type="text" name="name" value="{{ $user->name }}" required />
                        <x-input-error :messages="$errors->get('name')" class="error"/>
                    </div>

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('user.email')" />
                        <x-text-input id="email" type="email" name="email" value="{{ $user->email }}" disabled />
                        <x-input-error :messages="$errors->get('email')" class="error"/>
                    </div>

                    <!-- Password -->
                    <div>
                        <x-input-label for="password" :value="__('user.password')" />

                        <x-text-input id="password"
                                      type="password"
                                      name="password"
                                      required autocomplete="new-password" />

                        <x-input-error :messages="$errors->get('password')" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <x-input-label for="password_confirmation" :value="__('user.password_confirm')" />

                        <x-text-input id="password_confirmation"
                                      type="password"
                                      name="password_confirmation" required autocomplete="new-password" />

                        <x-input-error :messages="$errors->get('password_confirmation')" />
                    </div>

                    <!-- 2FA registration -->
                    <div>
                        <dt>@lang('user.one_time_password.title') 2FA</dt>
                        <dd id="userQr">
                            <a href="{{ $user->twoFactorQrCodeUrl() }}">
                                {!! $user->twoFactorQrCodeSvgWithAria() !!}
                            </a>
                        </dd>

                        <x-input-label for="two_factor_code" :value="__('user.one_time_password.code')" />

                        <x-text-input id="two_factor_code"
                                      type="text"
                                      name="two_factor_code" required />

                        <x-input-error :messages="$errors->get('two_factor_code')" />
                    </div>
                </fieldset>

                <input type="submit" value="{{__('user.register.button')}}">
            </form>
        </div>
    </section>
</x-guest-layout>
