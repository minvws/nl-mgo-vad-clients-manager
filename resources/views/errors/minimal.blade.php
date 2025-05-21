<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <!-- Scripts -->
    @vite(['resources/scss/app.scss'])
</head>

<body>

<main id="main-content" tabindex="-1">
    <div class="centered">
        <div class="one-third-two-thirds">
            <div class="centered">
                <x-tabler-exclamation-circle class="icon" width="75" height="75" aria-label="foutmelding" role="img"/>
            </div>
            <div>
                <h1>@yield('code')</h1>
                <p>@yield('message')</p>
            </div>
        </div>

    </div>
</main>
</body>

</html>

