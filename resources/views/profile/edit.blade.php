<x-app-layout>
    <x-slot name="heading">
        <h1>{{ __('profile.title') }}</h1>
    </x-slot>

    @include('profile.partials.update-profile-information-form')
    @include('profile.partials.update-password-form')
    @include('profile.partials.two-factor-authentication-form')
</x-app-layout>
