<?php

use Livewire\Component;
use Livewire\Attributes\Computed;

new class extends Component {
    public $visibleCount = 10;
    public $totalAlbums = 1248;

    protected $listeners = ['genre-changed', 'sort-changed' => 'refreshAlbums'];

    #[Computed]
    public function albums()
    {
        // In a real app, this would fetch from database
        return $this->getSampleAlbums();
    }

    #[Computed]
    public function displayAlbums()
    {
        return array_slice($this->albums, 0, $this->visibleCount);
    }

    public function refreshAlbums()
    {
        unset($this->albums);
        $this->dispatch('$refresh');
    }

    public function loadMore()
    {
        $this->visibleCount += 10;
    }

    private function getSampleAlbums()
    {
        return [
            [
                'image' => 'https://images.unsplash.com/photo-1501612780327-45045538702b?auto=format&fit=crop&w=600&q=80',
                'title' => 'Arctic Monkeys • Live at Luzhniki',
                'photographer' => 'Alex Petrov',
                'date' => 'Oct 15, 2025',
                'location' => 'Moscow',
                'photoCount' => 24,
                'rating' => 4.9,
                'badge' => '✨ NEW',
                'badgeGradient' => 'from-pink-500 to-orange-500',
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?auto=format&fit=crop&w=600&q=80',
                'title' => 'Swan Lake • Bolshoi Theater Premiere',
                'photographer' => 'Maria Volkova',
                'date' => 'Nov 2, 2025',
                'location' => 'Bolshoi',
                'photoCount' => 45,
                'rating' => 5.0,
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1459749411177-0473ef716170?auto=format&fit=crop&w=600&q=80',
                'title' => 'Park Live Festival 2025 • Day 2',
                'photographer' => 'Dmitry Sokolov',
                'date' => 'Aug 20, 2025',
                'location' => 'Park Live',
                'photoCount' => 203,
                'rating' => 4.8,
                'badge' => '🔥 FEATURED',
                'badgeGradient' => 'from-indigo-600 to-purple-600',
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=600&q=80',
                'title' => 'Igor Butman Quartet • Intimate Session',
                'photographer' => 'Elena Morozova',
                'date' => 'Sep 5, 2025',
                'location' => 'Jazz Club',
                'photoCount' => 18,
                'rating' => 4.7,
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1508700115892-45ecd05ae2ad?auto=format&fit=crop&w=600&q=80',
                'title' => 'Slayer Tribute • Final Tour Moscow',
                'photographer' => 'Ivan Kozlov',
                'date' => 'Oct 30, 2025',
                'location' => 'Arena Moscow',
                'photoCount' => 67,
                'rating' => 4.9,
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1514320291840-2e0a9bf2f4ae?auto=format&fit=crop&w=600&q=80',
                'title' => 'Hamlet • Taganka Theater Revival',
                'photographer' => 'Anna Fedorova',
                'date' => 'Nov 10, 2025',
                'location' => 'Taganka',
                'photoCount' => 31,
                'rating' => 4.8,
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?auto=format&fit=crop&w=600&q=80',
                'title' => 'Molchat Doma • 16 Tons Club',
                'photographer' => 'You (StagePhoto)',
                'date' => 'Nov 18, 2025',
                'location' => '16 Tons',
                'photoCount' => 39,
                'rating' => 5.0,
                'badge' => '👤 YOUR WORK',
                'badgeGradient' => 'from-emerald-500 to-teal-500',
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?auto=format&fit=crop&w=600&q=80',
                'title' => 'Valery Gergiev • Tchaikovsky Symphony No. 5',
                'photographer' => 'Sergei Ivanov',
                'date' => 'Oct 8, 2025',
                'location' => 'Tchaikovsky Hall',
                'photoCount' => 28,
                'rating' => 4.9,
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1516450360452-93659f5a3f21?auto=format&fit=crop&w=600&q=80',
                'title' => 'Nina Kraviz • Garage Live Set',
                'photographer' => 'Katya Smirnova',
                'date' => 'Nov 22, 2025',
                'location' => 'Garage Club',
                'photoCount' => 52,
                'rating' => 4.8,
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1504609773096-104ff2c73ba4?auto=format&fit=crop&w=600&q=80',
                'title' => 'Pelageya • Open Air Folk Fest',
                'photographer' => 'Pavel Orlov',
                'date' => 'Sep 28, 2025',
                'location' => 'Folk Festival',
                'photoCount' => 34,
                'rating' => 4.7,
            ],
        ];
    }
};

?>

<div>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-xl lg:text-2xl font-bold" x-text="t('latestAlbums')"></h1>
        <span class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 hidden sm:inline">{{ $totalAlbums }} <span x-text="t('albums')"></span> • 42,891 <span x-text="t('photos')"></span></span>
    </div>

    <div class="masonry-grid">
        @foreach($this->displayAlbums as $album)
            @livewire('ui.album-card', ['album' => $album], key(Str::random(10)))
        @endforeach
    </div>

    @if($visibleCount < count($this->albums))
        <div class="text-center py-8">
            <button
                wire:click="loadMore"
                class="px-6 py-3 text-sm font-medium text-white bg-gray-900 dark:bg-white dark:text-gray-900 hover:bg-gray-800 dark:hover:bg-gray-100 rounded-xl transition-colors shadow-md hover:shadow-lg flex items-center gap-2 mx-auto"
                data-loading-class="opacity-50"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span x-text="t('loadMore')"></span>
            </button>
            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                <span x-text="t('showing')"></span> {{ $visibleCount }} <span x-text="t('of')"></span> {{ $totalAlbums }} <span x-text="t('albums')"></span>
            </p>
        </div>
    @endif
</div>
