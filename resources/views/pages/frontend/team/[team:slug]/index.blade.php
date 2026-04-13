@page
@layout('layout')

@php
    // Override global team context with explicit route team
    $team = $team; // Already resolved by Folio route model binding
    abort_unless(auth()->user()->teams->contains($team), 403, 'Нет доступа к сообществу');

    $albums = $team->albums()->published()->latest('event_date')->paginate(12);
@endphp

<section class="px-4 sm:px-6 lg:px-8 py-6">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">{{ $team->name }} — Альбомы</h1>
        @can('manage', $team)
            <a href="{{ folio_route('team.settings') }}" class="text-stage-600 hover:underline">Управление</a>
        @endcan
    </div>

    <livewire:albums-grid :albums="$albums" :currentTeam="$team" />
    {{ $albums->links() }}
</section>
