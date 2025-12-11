@props([
    'organisations' => []
])

<x-app-layout>
    <x-slot name="heading">
        <h1>{{ __('client.create') }}</h1>
    </x-slot>

    <section>
        <div class="visually-grouped">
            <div>
                <form method="post" action="{{ route('clients.store') }}">
                    @csrf
                    <div>
                        <x-input-label for="organisation_id" :value="__('client.organisation')" />
                        <x-searchable-select
                            name="organisation_id"
                            :options="$organisations->pluck('name', 'id')"
                            :selected="old('organisation_id')"
                            :placeholder="__('client.organisation')"
                            :searchPlaceholder="__('client.search_organisation')"
                            required
                        />
                        <x-input-error :messages="$errors->get('organisation_id')" class="error" />
                    </div>
                    <div>
                    <div>
                        <x-input-label for="token_endpoint_auth_method" :value="__('client.token_endpoint_auth_method')" />
                        <x-searchable-select
                            name="token_endpoint_auth_method"
                            :options="App\Enums\TokenEndpointAuthMethod::toArray()"
                            :selected="old('token_endpoint_auth_method', 'none')"
                            :placeholder="__('client.token_endpoint_auth_method')"
                            required
                        />
                        <x-input-error :messages="$errors->get('token_endpoint_auth_method')" class="error" />
                    </div>
                    <div>
                        <x-input-label for="redirect_uris" :value="__('client.redirect_uris')" />
                        <p class="help-text">{{ __('client.redirect_uris_help') }}</p>
                        <x-repeater :items="old('redirect_uris', [''])" type="url" name="redirect_uris" />
                        <x-input-error :messages="$errors->first('redirect_uris')" class="error" />
                        <x-input-error :messages="$errors->first('redirect_uris.*')" class="error" />
                    </div>
                    <div class="toggle-container">
                        <x-input-label for="active" :value="__('client.active')" class="toggle-label" />
                        <x-toggle-switch name="active" :checked="old('active')" />
                        <x-input-error :messages="$errors->get('active')" class="error" />
                    </div>
                    <x-primary-button>
                        {{ __('client.create') }}
                    </x-primary-button>
                </form>
            </div>
        </div>
    </section>
</x-app-layout>
