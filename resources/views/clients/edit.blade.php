@props([
    'client',
    'organisations' => []
])

<x-app-layout>
    <x-slot name="heading">
        <h1>{{ __('client.edit') }}</h1>
    </x-slot>

    <section>
        <div class="visually-grouped">
            <div>
                <form method="POST" action="{{ route('clients.update', $client->id) }}">
                    @csrf
                    @method('PUT')
                    <div>
                        <x-input-label for="id" :value="__('client.id')" />
                        <x-text-input id="id" type="text" name="id" :value="$client->id" readonly />
                    </div>
                    <div>
                        <x-input-label for="organisation_id" :value="__('client.organisation')" />
                        <x-searchable-select
                            name="organisation_id"
                            :options="$organisations->pluck('name', 'id')"
                            :selected="$client->organisation_id"
                            :placeholder="__('client.organisation')"
                            :searchPlaceholder="__('client.search_organisation')"
                            required
                        />
                        <x-input-error :messages="$errors->get('organisation_id')" class="error" />
                    </div>
                    <div>
                        <x-input-label for="fqdn" :value="__('client.fqdn')" />
                        <x-text-input id="fqdn" type="text" name="fqdn" :value="old('fqdn', $client->fqdn)" required />
                        <x-input-error :messages="$errors->get('fqdn')" class="error" />
                    </div>
                    <div>
                        <x-input-label for="redirect_uris" :value="__('client.redirect_uris')" />
                        <p class="help-text">{{ __('client.redirect_uris_help') }}</p>
                        <x-repeater :items="old('redirect_uris', $client->redirect_uris)" type="url" name="redirect_uris" />
                        <x-input-error :messages="$errors->first('redirect_uris')" class="error" />
                        <x-input-error :messages="$errors->first('redirect_uris.*')" class="error" />
                    </div>
                    <div class="toggle-container">
                        <x-input-label for="active" :value="__('client.active')" class="toggle-label" />
                        <x-toggle-switch name="active" :checked="old('active', $client->active)" />
                        <x-input-error :messages="$errors->get('active')" class="error" />
                    </div>
                    <div>
                        <x-input-label for="created_at" :value="__('client.created_at')" />
                        <p>{{ $client->created_at }}</p>
                    </div>
                    <div>
                        <x-input-label for="updated_at" :value="__('client.updated_at')" />
                        <p>{{ $client->updated_at }}</p>
                    </div>
                    <x-primary-button>
                        {{ __('client.edit') }}
                    </x-primary-button>
                </form>
            </div>
        </div>
    </section>
</x-app-layout>
