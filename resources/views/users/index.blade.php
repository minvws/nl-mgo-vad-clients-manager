<x-app-layout>
    <x-slot name="heading">
        <div class="action-bar">
            <h1>{{ __('user.model_plural') }}</h1>
            <a class="button" href="{{ route('users.create') }}">{{ __('general.create') }}</a>
        </div>
    </x-slot>

    <section>
        <div class="visually-grouped">
            <table>
                <thead>
                    <tr>
                        <th scope="col">{{ __('user.name') }}</th>
                        <th scope="col">{{ __('user.email') }}</th>
                        <th scope="col">{{ __('user.role') }}</th>
                        <th scope="col">{{ __('user.active') }}</th>
                        <th scope="col">{{ __('user.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr>
                        <th scope="row">{{ $user->name }}</th>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->roleList() }}</td>
                        <td>
                            @if($user->active)
                                <x-tabler-circle-check fill="green" />
                            @else
                                <x-tabler-circle-x  fill="red" />
                            @endif
                        </td>
                        <td class="action-buttons">
                            @if($user->id !== $currentUser->id)
                                <a href="{{ route('users.edit', ['user' => $user->id]) }}" class="icon-only">
                                    <x-tabler-pencil aria-label="{{ __('general.edit') }}" role="img" />
                                </a>
                                <a href="{{ route('users.delete', ['user' => $user->id]) }}" class="icon-only">
                                    <x-tabler-trash-x aria-label="{{ __('general.delete') }}" role="img" />
                                </a>
                                <a href="{{ route('users.reset', ['user' => $user->id]) }}" class="icon-only">
                                    <x-tabler-refresh-alert aria-label="{{ __('general.reset') }}" role="img" />
                                </a>
                                
                                @can('changeActiveStatus', $user)
                                    @if($user->active)
                                        <a href="{{ route('users.deactivate', ['user'=>$user->id]) }}" class="icon-only">
                                            <x-tabler-circle-x  aria-label="{{ __('user.deactivate') }}" role="img" />
                                        </a>
                                    @else
                                        <a href="{{ route('users.activate', ['user'=>$user->id]) }}" class="icon-only">
                                            <x-tabler-circle-check aria-label="{{ __('user.activate') }}" role="img" />
                                        </a>
                                    @endif
                                @endcan()
                            @endif

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $users->links() }}
        </div>
    </section>
</x-app-layout>
