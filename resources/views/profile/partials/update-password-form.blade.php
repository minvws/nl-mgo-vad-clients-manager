<section class="visually-grouped">
    <div>
        <h2>
            {{ __('profile.password.title') }}
        </h2>

        <p>
            {{ __('profile.password.subtitle') }}
        </p>
    </div>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('profile.password.password_current')"/>
            <x-text-input id="update_password_current_password" name="current_password" type="password"
                          autocomplete="current-password"/>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="error"/>
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('profile.password.password_new')"/>
            <x-text-input id="update_password_password" name="password" type="password"
                          autocomplete="new-password"/>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="error"/>
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation"
                           :value="__('profile.password.password_confirm')"/>
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password"
                          autocomplete="new-password"/>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="error"/>
        </div>

        <div>
            <x-primary-button>{{ __('general.save') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                >{{ __('general.saved') }}</p>
            @endif
        </div>
    </form>
</section>
