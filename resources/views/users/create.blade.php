<x-app-layout>
    <x-slot name="heading">
        <h1>{{ __('user.create') }}</h1>
    </x-slot>

    <section>
        <div class="visually-grouped">
            <div>

                <form method="post" action="{{ route('users.store') }}">
                    @csrf
                    <div>
                        <x-input-label for="name" :value="__('user.name')" />
                            <x-text-input id="name" type="text" name="name" :value="old('name')" required />
                            <x-input-error :messages="$errors->get('name')" class="error"/>
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('user.email')" />
                            <x-text-input id="email" type="email" name="email" :value="old('email')" required />
                            <x-input-error :messages="$errors->get('email')" class="error"/>
                            </div>

                    <fieldset>
                        <legend>{{ __('role.model_plural') }}</legend>
                        @foreach ($roles as $role)
                        <div class="checkbox">
                            <input type="checkbox" id="{{ $role->value }}" name="roles[]" value="{{ $role->value }}" />
                            <x-input-label for="{{ $role->value }}" :value="__('role.'.$role->value)" />
                            </div>
                            @endforeach
                            <x-input-error :messages="$errors->get('roles')" />
                            </fieldset>

                        <div>
                            {{ __('user.create_description') }}
                        </div>
                        <x-primary-button>
                            {{ __('user.create') }}
                        </x-primary-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
