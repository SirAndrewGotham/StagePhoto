@page
@layout('layout')

@php
    // Ensure photographer exists & has role
    abort_unless($user->hasRole('photographer'), 404);

    // Team-aware visibility: show team albums first if context matches
    $albums = $user->albums()
        ->published()
        ->when($team, fn($q) => $q->orderByDesc('team_id', $team->id))
        ->latest('event_date')
        ->paginate(12);
@endphp

<section class="px-4 sm:px-6 lg:px-8 py-6">
    {{-- Photographer Hero --}}
    <div class="flex flex-col md:flex-row gap-6 mb-8">
        <img src="{{ $user->avatar_url ?? asset('img/default-avatar.jpg') }}"
             class="w-32 h-32 rounded-full object-cover border-4 border-stage-500/20">
        <div>
            <h1 class="text-3xl font-bold">{{ $user->name }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $user->bio ?? 'Концертный фотограф' }}</p>
            @if($team)
                <span class="inline-block mt-2 px-3 py-1 text-xs font-semibold rounded-full bg-stage-100 dark:bg-stage-900/30 text-stage-700 dark:text-stage-300">
                    📍 {{ $team->name }}
                </span>
            @endif
        </div>
    </div>

    {{-- Albums Grid --}}
    <livewire:albums-grid :albums="$albums" :currentTeam="$team" />
    {{ $albums->links() }}
</section>
