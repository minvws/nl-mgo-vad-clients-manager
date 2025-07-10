<x-guest-layout>
    <section class="centered">
        <div class="visually-grouped">
            <h2>{{ __('registration_request.title') }}</h2>

            <p>{{ __('registration_request.description.purpose') }}</p>
            <p>{{ __('registration_request.description.process') }}</p>

            <form method="POST" action="{{ route('registration-requests.store') }}">
                @csrf

                <div>
                    <x-input-label for="organisation_name" :value="__('registration_request.organisation_name')" />
                    <x-text-input id="organisation_name" name="organisation_name" type="text" class="mt-1 block w-full" :value="old('organisation_name')" required autofocus />
                    <x-input-error :messages="$errors->get('organisation_name')" />
                </div>

                <div>
                    <x-input-label for="organisation_main_contact_name" :value="__('registration_request.organisation_main_contact_name')" />
                    <x-text-input id="organisation_main_contact_name" name="organisation_main_contact_name" type="text" class="mt-1 block w-full" :value="old('organisation_main_contact_name')" required />
                    <x-input-error :messages="$errors->get('organisation_main_contact_name')" />
                </div>

                <div>
                    <x-input-label for="organisation_main_contact_email" :value="__('registration_request.organisation_main_contact_email')" />
                    <x-text-input id="organisation_main_contact_email" name="organisation_main_contact_email" type="email" class="mt-1 block w-full" :value="old('organisation_main_contact_email')" required />
                    <x-input-error :messages="$errors->get('organisation_main_contact_email')" />
                </div>

                <div>
                    <x-input-label for="organisation_coc_number" :value="__('registration_request.organisation_coc_number')" />
                    <x-text-input id="organisation_coc_number" name="organisation_coc_number" type="text" class="mt-1 block w-full" :value="old('organisation_coc_number')" required />
                    <x-input-error :messages="$errors->get('organisation_coc_number')" />
                </div>

                <div>
                    <x-input-label for="client_fqdn" :value="__('registration_request.client_fqdn')" />
                    <x-text-input id="client_fqdn" name="client_fqdn" type="text" class="mt-1 block w-full" :value="old('client_fqdn')" required />
                    <p>{{ __('registration_request.client_fqdn_help') }}</p>
                    <x-input-error class="mt-2" :messages="$errors->get('client_fqdn')" />
                </div>

                <div>
                    <x-input-label for="client_redirect_uris" :value="__('registration_request.client_redirect_uris')" />
                    <p class="help-text">{{ __('registration_request.client_redirect_uris_help') }}</p>
                    <x-repeater :items="old('client_redirect_uris', [''])" type="url" name="client_redirect_uris" />
                    <x-input-error :messages="$errors->first('client_redirect_uris')" class="error" />
                    <x-input-error :messages="$errors->first('client_redirect_uris.*')" class="error" />
                </div>

                <x-primary-button>
                    {{ __('registration_request.submit') }}
                </x-primary-button>
            </form>
        </div>
    </section>
</x-guest-layout> 