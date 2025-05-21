<x-app-layout>
    <x-slot name="heading">
        <h1>{{ __('user.edit_title', ['name' => $user->name]) }}</h1>
    </x-slot>

    <section>
        <div class="visually-grouped">
            <form method="post" action="{{ route('users.update', ['user' => $user->id]) }}">
                @csrf
                <fieldset>
                    <legend>{{ __('user.model_singular') }}</legend>
                    <div>
                        <x-input-label for="name" :value="__('user.name')" />
                        <x-text-input id="name" type="text" name="name" value="{{ $user->name }}" required />
                        <x-input-error :messages="$errors->get('name')" class="error"/>
                    </div>
                    <div>
                        <x-input-label for="email" :value="__('user.email')" />
                        <x-text-input id="email" type="email" name="email" value="{{ $user->email }}" required />
                        <x-input-error :messages="$errors->get('email')" class="error"/>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>{{ __('role.model_plural') }}</legend>
                    @foreach ($availableRoles as $role)
                        <div class="checkbox">
                            <input type="checkbox" id="{{ $role->value }}" name="roles[]" value="{{ $role->value }}" @checked($user->hasRole($role)) />
                            <x-input-label for="{{ $role->value }}" :value="__('role.'.$role->value)" />
                        </div>
                    @endforeach
                </fieldset>

                <input type="submit" value="{{__('general.save')}}">
            </form>
        </div>
    </section>
</x-app-layout>
