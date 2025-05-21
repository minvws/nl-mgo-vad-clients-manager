<nav
    data-open-label="Menu"
    data-close-label="Sluit menu"
    data-media="(min-width: 50rem)"
    class="collapsible"
    aria-label="@lang('Main Navigation')"
    id="main-nav">
        @auth
            <ul>
                <x-nav-item :route="'dashboard'">
                    {{ __('general.dashboard') }}
                </x-nav-item>
                @can('index', \App\Models\User::class)
                <x-nav-item :route="'users.index'" :active="request()->routeIs('users.*')">
                    {{ __('user.model_plural') }}
                </x-nav-item>
                @endcan
                <x-nav-item :route="'organisations.index'" :active="request()->routeIs('organisations.*')">
                    {{ __('organisation.model_plural') }}
                </x-nav-item>
                <x-nav-item :route="'clients.index'" :active="request()->routeIs('clients.*')">
                    {{ __('client.model_plural') }}
                </x-nav-item>
            </ul>

            <ul class="actions">
                <li class="nav-item">
                    <x-nav-link class="avatar" :href="route('profile.edit')">
                        <span class="visually-hidden">{{ __('profile.profile_of') }}: </span>
                        <span aria-hidden="true">{{ Str::initials(Auth::user()->name)  }}</span>
                        <span class="visually-hidden">{{ Auth::user()->name }}</span>
                    </x-nav-link>
                </li>
                <li class="nav-item">
                    <form method="post" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit">
                            {{ __('authentication.logout') }}
                        </button>
                    </form>
                </li>
            </ul>
        @endauth
</nav>

