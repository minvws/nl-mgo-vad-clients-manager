@props([
    'search' => '',
    'sort',
    'direction',
    'active' => null
])

<x-app-layout>
    <x-slot name="heading">
        <div class="action-bar">
            <h1>{{ __('client.model_plural') }}</h1>
            <a class="button" href="{{ route('clients.create') }}">{{ __('general.create') }}</a>
        </div>
    </x-slot>

    <section>
        <div class="search-container">
            <form method="GET" action="{{ route('clients.index') }}" class="search-form" id="client-filter-form">
                <div class="form-group">
                    <label for="search">{{ __('general.search') }}</label>
                    <input type="text" id="search" name="search" value="{{ $search ?? '' }}" placeholder="{{ __('client.search_placeholder') }}">
                    <button type="submit" class="button">{{ __('general.search') }}</button>
                </div>
                <div class="horizontal-view">
                    <label for="active">{{ __('client.active') }}</label>
                    <select name="active" id="active" class="form-control">
                        <option value="">{{ __('client.active_filter.all') }}</option>
                        <option value="1" {{ $active === true ? 'selected' : '' }}>{{ __('client.active_filter.active') }}</option>
                        <option value="0" {{ $active === false ? 'selected' : '' }}>{{ __('client.active_filter.inactive') }}</option>
                    </select>
                </div>
            </form>
        </div>

        <div class="visually-grouped">
            <table>
                <thead>
                <tr>
                    <th scope="col">{{ __('client.id') }}</th>
                    <x-sortable-header
                        route="clients.index"
                        :sort="$sort"
                        :direction="$direction"
                        :search="$search"
                        :active="$active"
                        sortField="organisations.name"
                        :label="__('client.owner_organisation.name')"
                    />
                    <x-sortable-header
                        route="clients.index"
                        :sort="$sort"
                        :direction="$direction"
                        :search="$search"
                        :active="$active"
                        sortField="organisations.main_contact_email"
                        :label="__('client.owner_organisation.main_contact_email')"
                    />
                    <x-sortable-header
                        route="clients.index"
                        :sort="$sort"
                        :direction="$direction"
                        :search="$search"
                        :active="$active"
                        sortField="clients.fqdn"
                        :label="__('client.fqdn')"
                    />
                    <th scope="col">{{ __('client.redirect_uris') }}</th>
                    <x-sortable-header
                        route="clients.index"
                        :sort="$sort"
                        :direction="$direction"
                        :search="$search"
                        :active="$active"
                        sortField="clients.created_at"
                        :label="__('client.created_at_header')"
                    />
                    <x-sortable-header
                        route="clients.index"
                        :sort="$sort"
                        :direction="$direction"
                        :search="$search"
                        :active="$active"
                        sortField="clients.updated_at"
                        :label="__('client.updated_at_header')"
                    />
                    <th scope="col">{{ __('client.active') }}</th>
                    <th scope="col">{{ __('client.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($clients as $client)
                    <tr>
                        <th scope="row">{{ $client->id }}</th>
                        <td>{{ $client->organisation->name}}</td>
                        <td>{{ $client->organisation->main_contact_email}}</td>
                        <td>{{ $client->fqdn }}</td>
                        <td>
                            <ul class="no-margin-padding">
                                @foreach($client->redirect_uris as $uri)
                                    <li>{{ $uri }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>{{ $client->created_at }}</td>
                        <td>{{ $client->updated_at }}</td>
                        <td>
                            @if($client->active)
                                <x-tabler-circle-check fill="green" />
                            @else
                                <x-tabler-circle-x  fill="red" />
                            @endif
                        </td>
                        <td class="action-buttons">
                            <a href="{{ route('clients.edit', ['client' => $client->id]) }}" class="icon-only">
                                <x-tabler-pencil aria-label="{{ __('general.edit') }}" role="img" />
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination-links">
            {{ $clients->links() }}
        </div>
    </section>

    @push('scripts')
    <script nonce="{{ csp_nonce() }}">
        document.getElementById('active').addEventListener('change', function() {
            document.getElementById('client-filter-form').submit();
        });
    </script>
    @endpush
</x-app-layout>
