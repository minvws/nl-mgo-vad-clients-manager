<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="csp-nonce" content="{{ csp_nonce() }}">

    <title>{{ $appName }}</title>

    <!-- Scripts -->
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>

<body>
<x-header>
    <x-navigation/>
</x-header>
<!-- Page Content -->
<main class="{{ ($withSidemenu ?? false) ? 'sidemenu' : ''}} mt-5" id="main-content" tabindex="-1">
    <x-flash/>

    <!-- Page Heading -->
    @isset($heading)
        {{ $heading }}
    @endisset

    {{ $slot }}
</main>
<x-footer/>
@stack('scripts')
</body>
</html>
