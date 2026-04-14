<?php

use Livewire\Component;

new class extends Component {
    public $album;

    public function mount($album): void
    {
        $this->album = $album;
    }

    public function viewAlbum()
    {
        $this->album->increment('views');
        return redirect()->to('/album/' . $this->album->slug);
    }
};

?>

<a href="/album/{{ $album->slug }}" wire:navigate class="block group">
    <article class="album-card relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-md hover:shadow-xl transition-all duration-300">
        <div class="relative h-48 overflow-hidden">
            <img
                src="{{ $album->cover_image }}"
                alt="{{ $album->title }}"
                class="album-cover w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                loading="lazy"
            >
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                <div class="text-white">
                    <p class="font-semibold text-sm">📷 {{ $album->photographer->name ?? 'Unknown' }}</p>
                    <p class="text-xs opacity-90">{{ $album->event_date->format('M d, Y') }} • {{ $album->venue }}</p>
                </div>
            </div>
            @if($album->badge)
                <span class="absolute top-3 left-3 px-2 py-1 text-xs font-bold text-white bg-gradient-to-r {{ $album->badge_gradient ?? 'from-pink-500 to-orange-500' }} rounded-full shadow-lg animate-pulse-slow">{{ $album->badge }}</span>
            @endif
        </div>
        <div class="p-4">
            <h3 class="font-bold text-base lg:text-lg text-gray-900 dark:text-white group-hover:text-stage-600 transition-colors truncate">{{ $album->title }}</h3>
            <div class="flex items-center justify-between mt-2 text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                <span>📸 {{ $album->photo_count }} {{ __('album.photos') }}</span>
                <span class="flex items-center gap-1 text-amber-500">⭐ {{ number_format($album->rating, 1) }}</span>
            </div>
        </div>
    </article>
</a>
