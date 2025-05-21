<x-app-layout>
    <x-slot name="heading">
        <div class="action-bar">
            <h1>{{ __('organisation.model_plural') }}</h1>
            <a class="button" href="{{ route('organisations.create') }}">{{ __('general.create') }}</a>
        </div>
    </x-slot>
    <section>
        <div class="visually-grouped">
            <table>
                <thead>
                    <tr>
                        <th scope="col">{{ __('organisation.name') }}</th>
                        <th scope="col">{{ __('organisation.main_contact_name') }}</th>
                        <th scope="col">{{ __('organisation.main_contact_email') }}</th>
                        <th scope="col">{{ __('organisation.coc_number') }}</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($organisations as $organisation)
                    <tr>
                        <td>{{ $organisation->name }}</td>
                        <td>{{ $organisation->main_contact_name }}</td>
                        <td>{{ $organisation->main_contact_email }}</td>
                        <td>{{ $organisation->coc_number }}</td>
                        <td class="action-buttons">
                            <a href="{{ route('organisations.edit', ['organisation' => $organisation->id]) }}" class="icon-only">
                                <x-tabler-pencil aria-label="{{ __('general.edit') }}" role="img" />
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">{{ __('organisation.none') }}</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-app-layout>