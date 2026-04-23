<?php

use Livewire\Component;
use Livewire\Attributes\Computed;

new class extends Component {
    public $album;

    #[Computed]
    public function coverImage()
    {
        return $this->album->cover_image_square
            ?? $this->album->cover_image
            ?? 'https://placehold.co/600x400/1a1a2e/ffffff?text=No+Image';
    }

    #[Computed]
    public function albumUrl(): string
    {
        return route('album.show', ['album' => $this->album->slug]);
    }

    #[Computed]
    public function photographerName()
    {
        return $this->album->photographer->name ?? 'Unknown Photographer';
    }

    #[Computed]
    public function formattedDate(): ?string
    {
        return $this->album->event_date
            ? \Carbon\Carbon::parse($this->album->event_date)->format('M d, Y')
            : null;
    }
};

?>

<div class="album-card break-inside-avoid">
    <a href="{{ $this->albumUrl }}" class="group block">
        <div class="relative overflow-hidden rounded-xl bg-gray-100 dark:bg-gray-800">
            <!-- Badge -->
            @if($album->badge)
                <div class="absolute top-3 left-3 z-10">
                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-gradient-to-r {{ $album->badge_gradient ?? 'from-stage-500 to-orange-600' }} text-white shadow-md">
                        {{ $album->badge }}
                    </span>
                </div>
            @endif

            <!-- Cover Image -->
            <div class="aspect-square overflow-hidden">
                <img src="{{ $this->coverImage }}"
                     alt="{{ $album->title }}"
                     class="album-cover w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
            </div>

            <!-- Overlay with photo count -->
            <div class="absolute bottom-3 right-3 z-10">
                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full bg-black/60 text-white backdrop-blur-sm">
                    📷 {{ $album->photo_count ?? 0 }}
                </span>
            </div>
        </div>

        <!-- Album Info -->
        <div class="mt-3 space-y-1">
            <h3 class="font-semibold text-gray-900 dark:text-white line-clamp-1 group-hover:text-stage-600 transition-colors">
                {{ $album->title }}
            </h3>

            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                <span class="flex items-center gap-1">
                    👤 {{ $this->photographerName }}
                </span>

                @if($this->formattedDate)
                    <span class="flex items-center gap-1">
                        📅 {{ $this->formattedDate }}
                    </span>
                @endif
            </div>

            @if($album->rating > 0)
                <div class="flex items-center gap-1 text-xs">
                    <span class="text-yellow-500">⭐</span>
                    <span class="text-gray-600 dark:text-gray-400">{{ number_format($album->rating, 1) }}</span>
                    <span class="text-gray-400">({{ $album->views ?? 0 }} views)</span>
                </div>
            @endif
        </div>
    </a>
</div>
