<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Album;
use App\Models\Photo;
use App\Services\ImageProcessingService;

new class extends Component {
    use WithPagination;

    public $type = 'albums'; // 'albums' or 'photos'
    public $albumId;

    protected $imageService;

    public function boot(ImageProcessingService $imageService): void
    {
        $this->imageService = $imageService;
    }

    public function restoreAlbum($albumId): void
    {
        $album = Album::withTrashed()->find($albumId);
        if ($album && $album->photographer_id === auth()->id()) {
            $this->imageService->restoreAlbum($album);
            $this->dispatch('album-restored');
        }
    }

    public function forceDeleteAlbum($albumId): void
    {
        $album = Album::withTrashed()->find($albumId);
        if ($album && $album->photographer_id === auth()->id()) {
            $this->imageService->forceDeleteAlbum($album);
            $this->dispatch('album-permanently-deleted');
        }
    }

    public function restorePhoto($photoId): void
    {
        $photo = Photo::withTrashed()->find($photoId);
        if ($photo && $photo->album->photographer_id === auth()->id()) {
            $this->imageService->restorePhoto($photo);
            $this->dispatch('photo-restored');
        }
    }

    public function forceDeletePhoto($photoId): void
    {
        $photo = Photo::withTrashed()->find($photoId);
        if ($photo && $photo->album->photographer_id === auth()->id()) {
            $this->imageService->forceDeletePhoto($photo);
            $this->dispatch('photo-permanently-deleted');
        }
    }

    public function render(): string
    {
        if ($this->type === 'albums') {
            $items = Album::onlyTrashed()
                ->where('photographer_id', auth()->id())
                ->withCount('photos')
                ->orderBy('deleted_at', 'desc')
                ->paginate(15);
        } else {
            $query = Photo::onlyTrashed()
                ->whereHas('album', function($q) {
                    $q->where('photographer_id', auth()->id());
                });

            if ($this->albumId) {
                $query->where('album_id', $this->albumId);
            }

            $items = $query->with('album')
                ->orderBy('deleted_at', 'desc')
                ->paginate(15);
        }

        return <<<'HTML'
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Trash</h2>
                    <div class="flex gap-2">
                        <button
                            wire:click="$set('type', 'albums')"
                            class="px-3 py-1 rounded-lg transition-colors {{ $type === 'albums' ? 'bg-stage-600 text-white' : 'bg-gray-200 dark:bg-gray-700' }}"
                        >
                            Albums
                        </button>
                        <button
                            wire:click="$set('type', 'photos')"
                            class="px-3 py-1 rounded-lg transition-colors {{ $type === 'photos' ? 'bg-stage-600 text-white' : 'bg-gray-200 dark:bg-gray-700' }}"
                        >
                            Photos
                        </button>
                    </div>
                </div>

                @if($type === 'albums')
                    <div class="space-y-3">
                        @forelse($items as $album)
                            <div class="border dark:border-gray-700 rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-semibold">{{ $album->title }}</h3>
                                        <p class="text-sm text-gray-500">
                                            Deleted: {{ $album->deleted_at->diffForHumans() }}
                                            • {{ $album->photos_count }} photos
                                        </p>
                                    </div>
                                    <div class="flex gap-2">
                                        <button
                                            wire:click="restoreAlbum({{ $album->id }})"
                                            class="px-3 py-1 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700"
                                        >
                                            Restore
                                        </button>
                                        <button
                                            wire:click="forceDeleteAlbum({{ $album->id }})"
                                            wire:confirm="Are you sure? This will permanently delete all photos in this album."
                                            class="px-3 py-1 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700"
                                        >
                                            Delete Permanently
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-8">No deleted albums found.</p>
                        @endforelse
                    </div>
                @else
                    <div class="space-y-3">
                        @forelse($items as $photo)
                            <div class="border dark:border-gray-700 rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-semibold">{{ $photo->title ?? 'Untitled' }}</h3>
                                        <p class="text-sm text-gray-500">
                                            Album: {{ $photo->album->title }}
                                            • Deleted: {{ $photo->deleted_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    <div class="flex gap-2">
                                        <button
                                            wire:click="restorePhoto('{{ $photo->id }}')"
                                            class="px-3 py-1 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700"
                                        >
                                            Restore
                                        </button>
                                        <button
                                            wire:click="forceDeletePhoto('{{ $photo->id }}')"
                                            wire:confirm="Are you sure? This will permanently delete this photo."
                                            class="px-3 py-1 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700"
                                        >
                                            Delete Permanently
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-8">No deleted photos found.</p>
                        @endforelse
                    </div>
                @endif

                <div class="mt-4">
                    {{ $items->links() }}
                </div>
            </div>
        HTML;
    }
};
?>
