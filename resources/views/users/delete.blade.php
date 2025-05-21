<x-app-layout>
    <x-slot name="heading">
        <h1>{{ __('user.delete.title') }}</h1>
    </x-slot>

    <section>
        <div class="visually-grouped">
            <form method="post" action="{{ route('users.delete', ['user' => $user->id]) }}">
                <x-input-error-manon :messages="$errors->all()" class="error"/>
                @csrf
                @method('DELETE')
                <p>{{ __('user.delete.text', ['name' => $user->name]) }}</p>

                <input type="submit" class="destructive" value="{{__('general.delete')}}">
            </form>
        </div>
    </section>
</x-app-layout>
