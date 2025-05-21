<x-app-layout>
    <x-slot name="heading">
        <h1>{{ __('organisation.create') }}</h1>
    </x-slot>

    <section>
        <div class="visually-grouped">
            <div>
                <form method="post" action="{{ route('organisations.store') }}">
                    @csrf
                    <div>
                        <x-input-label for="name" :value="__('organisation.name')" />
                        <x-text-input id="name" type="text" name="name" :value="old('name')" required />
                        <x-input-error :messages="$errors->get('name')" class="error" />
                    </div>
                    <div>
                        <x-input-label for="main_contact_name" :value="__('organisation.main_contact_name')" />
                        <x-text-input id="main_contact_name" type="text" name="main_contact_name" :value="old('main_contact_name')"
                            required />
                        <x-input-error :messages="$errors->get('main_contact_name')" class="error" />
                    </div>
                    <div>
                        <x-input-label for="main_contact_email" :value="__('organisation.main_contact_email')" />
                        <x-text-input id="main_contact_email" type="email" name="main_contact_email" :value="old('main_contact_email')"
                            required />
                        <x-input-error :messages="$errors->get('main_contact_email')" class="error" />
                    </div>
                    <div>
                        <x-input-label for="coc_number" :value="__('organisation.coc_number')" />
                        <x-text-input id="coc_number" type="text" name="coc_number" :value="old('coc_number')" required
                            maxlength="8" minlength="8" />
                        <x-input-error :messages="$errors->get('coc_number')" class="error" />
                    </div>
                    <div>
                        <x-input-label for="notes" :value="__('organisation.notes')" />
                        <x-textarea id="notes" name="notes" cols="150" rows="5">
                            {{ old('notes') }}
                        </x-textarea>
                        <x-input-error :messages="$errors->get('notes')" class="error" />
                    </div>
                    <x-primary-button>
                        {{ __('organisation.create') }}
                    </x-primary-button>
                </form>
            </div>
        </div>
    </section>
</x-app-layout>
