<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MotoPart') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased" style="background-color:#EDDCC6;">

    <div class="min-h-screen">

        @include('layouts.navigation')

        @isset($header)
            <header style="background-color:#EDDCC6;" class="shadow-sm">
                <div class="container-fluid py-4 px-4 px-md-5 text-white font-semibold">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main class="container-fluid p-0">
            {{ $slot }}
        </main>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
