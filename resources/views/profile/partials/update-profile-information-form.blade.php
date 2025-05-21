<section class="visually-grouped">
    <div>
        <h2>
            {{ __('profile.personal.title') }}
        </h2>

        <p>
            {{ __('profile.personal.subtitle') }}
        </p>
    </div>

    <form method="post" action="{{ route('profile.edit') }}">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('user.name')"/>
            <x-text-input id="name" name="name" type="text" :value="old('name', $user->name)"
                          required autocomplete="name"/>
            <x-input-error :messages="$errors->get('name')"/>
        </div>

        <div>
            <x-input-label for="email" :value="__('user.email')"/>
            <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)"
                          required autocomplete="username"/>
            <x-input-error :messages="$errors->get('email')"/>
        </div>

        <div>
            <x-primary-button>{{ __('general.save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }"
                   x-show="show"
                   x-transition
                   x-init="setTimeout(() => show = false, 2000)"
                >{{ __('general.saved') }}</p>
            @endif
        </div>
    </form>
</section>
