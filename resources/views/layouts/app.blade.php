<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="bg-[var(--color-stage-50)] dark:bg-[var(--color-stage-900)] text-gray-900 dark:text-gray-100">

        {{ $slot }}

        @livewireScripts
    </body>
</html>
