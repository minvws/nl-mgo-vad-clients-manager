<x-app-layout>
    <x-slot name="heading">
        <h1>{{ __('user.reset.title') }}</h1>
    </x-slot>

    <section>
        <div class="visually-grouped">
            <form method="post" action="{{ route('users.reset', ['user' => $user->id]) }}">
                <x-input-error-manon :messages="$errors->all()" class="error"/>
                @csrf
                <p>{{ __('user.reset.text', ['name' => $user->name]) }}</p>

                <input type="submit" class="warning" value="{{__('user.reset.button_text')}}">
            </form>
        </div>
    </section>
</x-app-layout>
