<?php

use Livewire\Component;

new class extends Component {
    public $album;

    public function mount($album): void
    {
        $this->album = $album;
    }

    public function viewAlbum(): void
    {
        // Handle view album action
        $this->dispatch('view-album', albumId: $this->album['id'] ?? null);
    }

    public function requestAlbum(): void
    {
        $this->dispatch('request-album', albumId: $this->album['id'] ?? null);
    }
};

?>

<article class="album-card group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-md hover:shadow-xl transition-all duration-300">
    <div class="relative h-48 overflow-hidden">
        <img
            src="{{ $album['image'] }}"
            alt="{{ $album['title'] }}"
            class="album-cover w-full h-full object-cover"
            loading="lazy"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
            <div class="text-white">
                <p class="font-semibold text-sm">📷 {{ $album['photographer'] }}</p>
                <p class="text-xs opacity-90">{{ $album['date'] }} • {{ $album['location'] }}</p>
            </div>
        </div>
        @if($album['badge'] ?? false)
            <span class="absolute top-3 left-3 px-2 py-1 text-xs font-bold text-white bg-gradient-to-r {{ $album['badgeGradient'] ?? 'from-pink-500 to-orange-500' }} rounded-full shadow-lg animate-pulse-slow">{{ $album['badge'] }}</span>
        @endif
    </div>
    <div class="p-4">
        <h3 class="font-bold text-base lg:text-lg text-gray-900 dark:text-white truncate">{{ $album['title'] }}</h3>
        <div class="flex items-center justify-between mt-2 text-xs sm:text-sm text-gray-600 dark:text-gray-300">
            <span>📸 {{ $album['photoCount'] }} <span x-text="t('photos')"></span></span>
            <span class="flex items-center gap-1 text-amber-500">⭐ {{ $album['rating'] }}</span>
        </div>
        <div class="mt-3 flex gap-2">
            <button wire:click="viewAlbum" class="flex-1 px-3 py-1.5 text-center text-xs sm:text-sm font-medium text-white bg-stage-600 hover:bg-stage-700 rounded-lg transition-colors" x-text="t('viewAlbum')"></button>
            <button wire:click="requestAlbum" class="px-3 py-1.5 text-xs sm:text-sm font-medium text-stage-600 border border-stage-600 hover:bg-stage-50 dark:hover:bg-stage-900/30 rounded-lg transition-colors" x-text="t('request')"></button>
        </div>
    </div>
</article>
