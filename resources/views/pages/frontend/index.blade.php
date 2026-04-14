@page('Title')

<?php
use function Laravel\Folio\render;
use Illuminate\View\View;

render(function (View $view) {
    $team = null;

    if (auth()->check() && auth()->user()?->currentTeam) {
        $team = auth()->user()->currentTeam;
    } elseif (request()->route('team')) {
        $team = \App\Models\Team::where('slug', request()->route('team'))->first();
    }

    $albums = \App\Models\Album::published()
        ->when($team, fn($q) => $q->where('team_id', $team->id))
        ->orderByDesc('event_date')
        ->paginate(12);

    return $view->with(['team' => $team, 'albums' => $albums]);
});
?>

@extends('pages.frontend.layout')

@section('content')
    <section class="px-4 sm:px-6 lg:px-8 py-6">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold">Последние альбомы</h1>
            <span class="text-sm text-gray-500 dark:text-gray-400">
            {{-- ✅ Fixed: $albums is always a paginator from the query below --}}
                {{ $albums->total() }} альбомов
            </span>
        </div>

        @php
            $albums = \App\Models\Album::published()
                ->when($team ?? null, fn($q) => $q->where('team_id', $team?->id))
                ->orderByDesc('event_date')
                ->paginate(12);
        @endphp

        <livewire:albums-grid :albums="$albums" />
    </section>
@endsection
