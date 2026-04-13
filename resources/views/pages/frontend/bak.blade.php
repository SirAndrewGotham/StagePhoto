@page
@layout('layout')

<?php
use function Laravel\Folio\render;
use Illuminate\View\View;

render(function (View $view) {
    // Resolve team the same way your middleware does
    $team = null;
    if (auth()->check() && auth()->user()?->currentTeam) {
        $team = auth()->user()->currentTeam;
    } elseif (request()->route('team')) {
        $team = App\Models\Team::where('slug', request()->route('team'))->first();
    }

    return $view->with('team', $team);
});
?>

@php
    // Team-aware album query
    $albums = \App\Models\Album::published()
        ->when($team, fn($q) => $q->orWhere('team_id', $team->id))
        ->orderByDesc('event_date')
        ->paginate(12);
@endphp

<section class="px-4 sm:px-6 lg:px-8 py-6">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">
            @if($team)
                {{ $team->name }} — Альбомы
            @else
                Последние альбомы
            @endif
        </h1>
        <span class="text-sm text-gray-500 dark:text-gray-400">
            {{ number_format($albums->total()) }} альбомов
        </span>
    </div>

    {{-- Masonry Grid (Livewire/Alpine handled in component) --}}
    <livewire:albums-grid :albums="$albums" :currentTeam="$team" />

    {{-- Pagination / Load More --}}
    <div class="text-center py-8">
        {{ $albums->links() }}
    </div>
</section>
