<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $appName }}</title>

    <!-- Scripts -->
    @vite(['resources/scss/app.scss'])
</head>

<body>
    <x-header>
        <x-navigation />
    </x-header>

    <main class="authentication" id="main-content" tabindex="-1">
        <!-- Flash message -->
        <x-flash />

        <!-- Page Heading -->
        @isset($heading)
            {{ $heading }}
        @endisset

        {{ $slot }}
    </main>

    <x-footer />
</body>

</html>

