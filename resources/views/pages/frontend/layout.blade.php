@php
    // Resolve team context once per request
    $team = null;
    if (auth()->check()) {
        $user = auth()->user();
        $team = $user->currentTeam ?? $user->teams()->first();
        if ($team) $user->current_team_id = $team->id; // Keep session fresh
    }
@endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'StagePhoto.ru') — Концертная и театральная фотография</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @folioStyles
</head>
<body class="bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 transition-colors duration-300">
<!-- Header with Team Context -->
<x-frontend.header :currentTeam="$team" />

<!-- Filter Bar -->
{{--<x-filter-bar :team="$team" />--}}

<!-- Main Content Area -->
<main class="w-full">
    {{ $slot ?? '' }}
</main>

<!-- Footer -->
<x-frontend.footer />

@livewireScripts
@folioScripts
</body>
</html>
