@page
@layout('layout')

@php
    // Pass team context from global layout
    $teamId = auth()->user()?->current_team_id;
@endphp

<livewire:photographer-profile :photographer="$user" :current-team-id="$teamId" />
