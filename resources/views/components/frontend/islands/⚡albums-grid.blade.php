<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use App\Models\Album;
use Illuminate\Support\Str;

new class extends Component {
    public $visibleCount = 12; // Show 12 per page
    public $selectedGenre = 'all';
    public $selectedSort = 'mostRecent';

    #[On('genre-changed')]
    public function onGenreChanged($genre): void
    {
        $this->selectedGenre = $genre;
        $this->visibleCount = 12;
        $this->dispatch('$refresh');
    }

    #[On('sort-changed')]
    public function onSortChanged($sort): void
    {
        $this->selectedSort = $sort;
        $this->visibleCount = 12;
        $this->dispatch('$refresh');
    }

    #[Computed]
    public function albums()
    {
        $query = Album::with(['categories', 'photographer'])
            ->where('is_published', true);

        // Filter by genre
        if ($this->selectedGenre !== 'all') {
            $query->whereHas('categories', function($q) {
                $q->where('slug', $this->selectedGenre);
            });
        }

        // Apply sorting
        match ($this->selectedSort) {
            'mostViewed' => $query->orderBy('views', 'desc'),
            'topRated' => $query->orderBy('rating', 'desc'),
            'newPhotographers' => $query->whereHas('photographer', function($q) {
                $q->orderBy('created_at', 'desc');
            }),
            // mostRecent
            default => $query->orderBy('event_date', 'desc'),
        };

        return $query->get();
    }

    #[Computed]
    public function displayAlbums()
    {
        return $this->albums->slice(0, $this->visibleCount);
    }

    #[Computed]
    public function totalAlbumsCount()
    {
        return $this->albums->count();
    }

    public function loadMore(): void
    {
        $this->visibleCount += 12;
    }
};

?>

<div>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-xl lg:text-2xl font-bold" x-text="$store.i18n.t('latestAlbums')"></h1>
        <span class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 hidden sm:inline">
            {{ $this->totalAlbumsCount }} <span x-text="$store.i18n.t('albums')"></span>
        </span>
    </div>

    <div class="masonry-grid">
        @forelse($this->displayAlbums as $album)
            @livewire('ui.album-card', ['album' => $album], key($album->id))
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500 dark:text-gray-400">No albums found</p>
            </div>
        @endforelse
    </div>

    @if($this->visibleCount < $this->totalAlbumsCount)
        <div class="text-center py-8">
            <button
                wire:click="loadMore"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-50"
                class="px-6 py-3 text-sm font-medium text-white bg-gray-900 dark:bg-white dark:text-gray-900 hover:bg-gray-800 dark:hover:bg-gray-100 rounded-xl transition-colors shadow-md hover:shadow-lg flex items-center gap-2 mx-auto"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span wire:loading.remove x-text="$store.i18n.t('loadMore')"></span>
                <span wire:loading>
                    <svg class="animate-spin h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Loading...
                </span>
            </button>
            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                <span x-text="$store.i18n.t('showing')"></span> {{ $this->visibleCount }}
                <span x-text="$store.i18n.t('of')"></span> {{ $this->totalAlbumsCount }}
                <span x-text="$store.i18n.t('albums')"></span>
            </p>
        </div>
    @endif
</div>
