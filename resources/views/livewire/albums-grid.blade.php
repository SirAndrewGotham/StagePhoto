<?php

namespace App\Livewire;

use App\Models\Album;
use App\Models\Genre;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AlbumsGrid extends Component
{
    use WithPagination;

    public ?int $currentTeamId = null;
    public ?string $genre = null;
    #[Url(history: true, keep: true)] public string $sort = 'recent';
    #[Url(history: true, keep: true)] public string $search = '';
    public bool $hasMore = true;
    protected int $perPage = 12;

    public function mount(?int $currentTeamId = null): void
    {
        $this->currentTeamId = $currentTeamId;
    }

    public function updated($property): void
    {
        if (in_array($property, ['genre', 'search', 'sort'])) {
            $this->resetPage();
            $this->hasMore = true;
        }
    }

    public function loadMore(): void
    {
        if ($this->hasMore) {
            $this->page++;
        }
    }

    public function getAlbums()
    {
        $query = Album::published()->with(['photographer', 'genres']);

        // Team context scoping
        if ($this->currentTeamId) {
            $query->where('team_id', $this->currentTeamId);
        }

        // Genre filter
        if ($this->genre) {
            $query->whereHas('genres', fn($q) => $q->where('slug', $this->genre));
        }

        // Search
        if ($this->search !== '' && $this->search !== '0') {
            $query->where(function($q) {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('venue', 'like', "%{$this->search}%")
                    ->orWhere('city', 'like', "%{$this->search}%")
                    ->orWhereHas('photographer', fn($p) => $p->where('name', 'like', "%{$this->search}%"));
            });
        }

        return $query->orderBy(match($this->sort) {
            'popular' => 'views_count',
            'rating'  => 'avg_rating',
            'new'     => 'created_at',
            default   => 'event_date'
        }, 'desc')->paginate($this->perPage);
    }

    public function render()
    {
        $albums = $this->getAlbums();
        $genres = Genre::orderBy('name')->get();
        $this->hasMore = $albums->hasMorePages();

        return view('livewire.albums-grid', ['albums' => $albums, 'genres' => $genres]);
    }
}
?>

<div x-data="infiniteScroll(@js($albums->hasMorePages()))" class="w-full">
    {{-- Filter Bar --}}
    <div class="sticky top-16 z-40 bg-white/95 dark:bg-gray-900/95 backdrop-blur-sm border-b border-gray-200 dark:border-gray-800 py-3 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap gap-3">
            <div class="flex gap-2 overflow-x-auto flex-1 pb-1 scrollbar-thin">
                <button wire:click="$set('genre', null)"
                        :class="$wire.genre === null ? 'bg-stage-600 text-white shadow-md' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
                        class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors whitespace-nowrap">Все</button>
                @foreach($genres as $g)
                    <button wire:click="$set('genre', '{{ $g->slug }}')"
                            :class="$wire.genre === '{{ $g->slug }}' ? 'bg-stage-600 text-white shadow-md' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
                            class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors whitespace-nowrap">{{ $g->icon }} {{ $g->name }}</button>
                @endforeach
            </div>

            <select wire:model.live="sort" class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-stage-500">
                <option value="recent">📅 Недавние</option>
                <option value="popular">🔥 Популярные</option>
                <option value="rating">⭐ Лучшие</option>
                <option value="new">✨ Новые авторы</option>
            </select>

            <input wire:model.live.debounce.300ms="search" type="search"
                   placeholder="Поиск групп, площадок..."
                   class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-stage-500 w-40 sm:w-56 lg:w-64">
        </div>
    </div>

    {{-- Masonry Grid --}}
    <div class="px-4 sm:px-6 lg:px-8 py-6">
        <div class="columns-1 sm:columns-2 lg:columns-3 xl:columns-4 gap-4 lg:gap-6 space-y-4 lg:space-y-6">
            @foreach($albums as $album)
                <article class="break-inside-avoid group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-md hover:shadow-xl transition-all duration-300">
                    <div class="relative h-48 overflow-hidden">
                        <img src="{{ $album->photos->first()?->thumbnail_path ?? asset('img/placeholder-album.webp') }}"
                             alt="{{ $album->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                            <div class="text-white">
                                <p class="font-semibold text-sm">📷 {{ $album->photographer->name }}</p>
                                <p class="text-xs opacity-90">{{ $album->event_date->format('d.m.Y') }} • {{ $album->city }}</p>
                            </div>
                        </div>
                        @if($album->is_featured)
                            <span class="absolute top-3 left-3 px-2 py-1 text-xs font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 rounded-full shadow-lg">🔥 РЕКОМЕНДУЕМ</span>
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-lg text-gray-900 dark:text-white truncate">{{ $album->title }}</h3>
                        <div class="flex items-center justify-between mt-2 text-sm text-gray-600 dark:text-gray-300">
                            <span>📸 {{ $album->photos->count() }} фото</span>
                            <span class="flex items-center gap-1 text-amber-500">⭐ {{ number_format($album->avg_rating, 1) }}</span>
                        </div>
                        <a href="{{ route('album.show', $album->slug) }}" class="mt-3 block text-center px-3 py-1.5 text-sm font-medium text-white bg-stage-600 hover:bg-stage-700 rounded-lg transition-colors shadow-sm">Открыть альбом</a>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- Infinite Scroll Trigger --}}
        <div x-ref="sentinel" class="h-8"></div>
        <div x-show="loading" class="text-center py-6">
            <div class="inline-block h-6 w-6 animate-spin rounded-full border-2 border-stage-600 border-t-transparent"></div>
            <p class="mt-2 text-sm text-gray-500">Загрузка...</p>
        </div>
        <div x-show="!hasMore && !loading" class="text-center py-8 text-gray-500">
            <p>Больше альбомов нет 🎉</p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('infiniteScroll', (initialHasMore) => ({
            hasMore: initialHasMore,
            loading: false,
            observer: null,
            init() {
                this.observer = new IntersectionObserver(async (entries) => {
                    if (entries[0].isIntersecting && !this.loading && this.$wire.hasMore) {
                        this.loading = true;
                        await this.$wire.loadMore();
                        this.hasMore = this.$wire.hasMore;
                        this.loading = false;
                    }
                }, { rootMargin: '200px' });
                this.observer.observe(this.$refs.sentinel);
            }
        }));
    });
</script>
