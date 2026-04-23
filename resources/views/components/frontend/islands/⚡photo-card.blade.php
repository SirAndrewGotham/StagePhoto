<?php

use Livewire\Component;
use Livewire\Attributes\Computed;

new class extends Component {
    public $photo;

    #[Computed]
    public function thumbnailUrl()
    {
        return $this->photo->thumbnail_path
            ? Storage::url($this->photo->thumbnail_path)
            : 'https://placehold.co/600x400/1a1a2e/ffffff?text=No+Image';
    }

    #[Computed]
    public function photoUrl(): string
    {
        return route('photo.show', ['photo' => $this->photo->id]);
    }

    #[Computed]
    public function albumTitle()
    {
        return $this->photo->album->title ?? 'Unknown Album';
    }
};

?>

<div class="photo-card break-inside-avoid">
    <a href="{{ $this->photoUrl }}" class="group block">
        <div class="relative overflow-hidden rounded-xl bg-gray-100 dark:bg-gray-800">
            <!-- Thumbnail -->
            <div class="aspect-square overflow-hidden">
                <img src="{{ $this->thumbnailUrl }}"
                     alt="{{ $this->photo->title ?? 'Photo' }}"
                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
            </div>

            <!-- Overlay with album info -->
            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent p-3 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                <p class="text-xs text-white line-clamp-2">
                    {{ $this->albumTitle }}
                </p>
            </div>
        </div>

        <!-- Photo Title -->
        @if($this->photo->title)
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 line-clamp-1 text-center">
                {{ $this->photo->title }}
            </p>
        @endif
    </a>
</div>
