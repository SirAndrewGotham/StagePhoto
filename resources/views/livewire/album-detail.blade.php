<?php

namespace App\Livewire;

use App\Models\Album;
use Livewire\Attributes\Computed;
use Livewire\Component;

class AlbumDetail extends Component
{
    public Album $album;

    public function mount(Album $album): void
    {
        $this->album = $album->load([
            'photographer',
            'genres',
            'photos' => fn($q) => $q->orderBy('sort_order')
        ]);

        // Safe view counter
        $this->album->increment('views_count');
    }

    #[Computed]
    public function photos(): array
    {
        return $this->album->photos->map(fn($p) => [
            'id'      => $p->id,
            'thumb'   => $p->thumbnail_path,
            'full'    => $p->optimized_path ?? $p->original_path,
            'caption' => $p->caption ?? '',
        ])->values()->all();
    }

    public function render(): mixed
    {
        return view('livewire.album-detail');
    }
}
?>

<div x-data="albumLightbox({{ json_encode($this->photos) }})" class="w-full">
    {{-- Album Header --}}
    <header class="px-4 sm:px-6 lg:px-8 py-8 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-5xl mx-auto">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">{{ $album->title }}</h1>
            <div class="mt-2 flex flex-wrap gap-3 text-sm text-gray-600 dark:text-gray-400">
                <span>📷 <a href="{{ route('photographer.show', $album->photographer->username ?? $album->photographer->slug) }}" class="hover:text-stage-600 underline">{{ $album->photographer->name }}</a></span>
                <span>📅 {{ $album->event_date->format('d M Y') }}</span>
                @if($album->venue) <span>📍 {{ $album->venue }}@if($album->city), {{ $album->city }}@endif</span> @endif
                <span>👁️ {{ number_format($album->views_count) }}</span>
                <span class="text-amber-500">⭐ {{ number_format($album->avg_rating, 1) }}</span>
            </div>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach($album->genres as $genre)
                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                        {{ $genre->icon }} {{ $genre->name }}
                    </span>
                @endforeach
            </div>
        </div>
    </header>

    {{-- Photo Grid --}}
    <section class="px-4 sm:px-6 lg:px-8 py-6">
        <div class="columns-2 sm:columns-3 lg:columns-4 xl:columns-5 gap-3 space-y-3">
            @foreach($this->photos as $index => $photo)
                <div class="break-inside-avoid mb-3 cursor-pointer group relative rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-800 aspect-[4/3]"
                     @click="open({{ $index }})">
                    <img src="{{ $photo['thumb'] }}"
                         alt="{{ $photo['caption'] }}"
                         loading="lazy"
                         class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-300">
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors"></div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Lightbox Modal --}}
    <div x-show="isOpen"
         x-cloak
         @keydown.window.escape="close()"
         @keydown.window.arrow-left.prevent="prev()"
         @keydown.window.arrow-right.prevent="next()"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/95 backdrop-blur-md p-2 sm:p-6"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        {{-- Close --}}
        <button @click="close()" class="absolute top-3 right-3 sm:top-4 sm:right-6 p-2 text-white/70 hover:text-white bg-white/10 hover:bg-white/20 rounded-full transition" aria-label="Закрыть">✕</button>

        {{-- Navigation --}}
        <button @click="prev()" class="absolute left-2 sm:left-6 p-3 text-white/70 hover:text-white bg-white/10 hover:bg-white/20 rounded-full transition disabled:opacity-30" :disabled="photos.length <= 1" aria-label="Предыдущее">←</button>
        <button @click="next()" class="absolute right-2 sm:right-6 p-3 text-white/70 hover:text-white bg-white/10 hover:bg-white/20 rounded-full transition disabled:opacity-30" :disabled="photos.length <= 1" aria-label="Следующее">→</button>

        {{-- Image Container --}}
        <div class="relative w-full max-w-6xl max-h-[90vh] flex flex-col items-center justify-center"
             @touchstart.passive="touchStart = $event.touches[0].clientX"
             @touchend.passive="handleSwipe($event.changedTouches[0].clientX)">
            <img :src="currentPhoto.full"
                 :alt="currentPhoto.caption"
                 class="max-w-full max-h-[80vh] object-contain rounded-lg shadow-2xl select-none cursor-zoom-in"
                 loading="eager"
                 @click="zoomed = !zoomed"
                 :class="{ 'scale-150': zoomed }">

            <div class="mt-3 text-center text-white/80 text-sm bg-black/40 px-3 py-1.5 rounded-full backdrop-blur-sm">
                <span x-text="currentPhoto.caption"></span>
                <span x-show="currentPhoto.caption && photos.length > 1" class="mx-2">•</span>
                <span x-text="`${currentIndex + 1} / ${photos.length}`"></span>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('albumLightbox', (photos) => ({
            photos,
            isOpen: false,
            currentIndex: 0,
            zoomed: false,
            touchStart: 0,
            preloadImage: new Image(),

            get currentPhoto() {
                return this.photos[this.currentIndex] || { full: '', caption: '' };
            },

            init() {
                // Preload next/prev on open
                this.$watch('currentIndex', () => {
                    if (this.isOpen && this.photos.length > 1) {
                        this.preloadImage.src = this.photos[(this.currentIndex + 1) % this.photos.length].full;
                        this.preloadImage.src = this.photos[(this.currentIndex - 1 + this.photos.length) % this.photos.length].full;
                    }
                });
            },

            open(index) {
                this.currentIndex = index;
                this.isOpen = true;
                this.zoomed = false;
                document.body.style.overflow = 'hidden';
            },

            close() {
                this.isOpen = false;
                this.zoomed = false;
                document.body.style.overflow = '';
            },

            next() {
                if (this.photos.length <= 1) return;
                this.currentIndex = (this.currentIndex + 1) % this.photos.length;
                this.zoomed = false;
            },

            prev() {
                if (this.photos.length <= 1) return;
                this.currentIndex = (this.currentIndex - 1 + this.photos.length) % this.photos.length;
                this.zoomed = false;
            },

            handleSwipe(endX) {
                const diff = this.touchStart - endX;
                if (Math.abs(diff) > 50) diff > 0 ? this.next() : this.prev();
            }
        }));
    });
</script>
