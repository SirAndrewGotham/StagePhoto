<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Services\ImageProcessingService;
use App\Services\UnsortedAlbumService;
use App\Models\Album;

new class extends Component {
    use WithFileUploads;

    public $currentTeam;

    // Album selection
    public $selectedAlbumId;
    public $createNewAlbum = false;
    public $newAlbumTitle = '';

    // Multiple photos
    public $photos = [];

    // Upload state
    public $isProcessing = false;
    public $results = [];
    public $showSuccessModal = false;
    public $successMessage = '';
    public $errorMessage = '';

    protected $imageService;
    protected $unsortedService;

    public function boot(
        ImageProcessingService $imageService,
        UnsortedAlbumService $unsortedService
    ): void {
        $this->imageService = $imageService;
        $this->unsortedService = $unsortedService;
    }

    public function mount($currentTeam = null): void
    {
        $this->currentTeam = $currentTeam;
    }

    public function getUserAlbumsProperty()
    {
        return Album::where('photographer_id', auth()->id())
            ->where('is_unsorted', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function removePhoto($index): void
    {
        unset($this->photos[$index]);
        $this->photos = array_values($this->photos);
    }

    public function save(): void
    {
        $this->validate([
            'photos.*' => 'required|image|max:51200',
            'photos' => 'required|array|min:1',
        ]);

        $this->isProcessing = true;
        $this->errorMessage = '';
        $this->results = [];

        try {
            if ($this->createNewAlbum) {
                $this->validate(['newAlbumTitle' => 'required|string|min:3|max:255']);

                $album = Album::create([
                    'title' => $this->newAlbumTitle,
                    'slug' => Str::slug($this->newAlbumTitle) . '-' . uniqid(),
                    'photographer_id' => auth()->id(),
                    'event_date' => now(),
                    'is_published' => false,
                ]);
            } else {
                $this->validate(['selectedAlbumId' => 'required|exists:albums,id']);
                $album = Album::findOrFail($this->selectedAlbumId);
            }

            $uploadedCount = 0;
            $failedPhotos = [];

            foreach ($this->photos as $photo) {
                try {
                    $title = pathinfo((string) $photo->getClientOriginalName(), PATHINFO_FILENAME);

                    $this->imageService->processUpload(
                        $photo,
                        $album,
                        $title,
                        null
                    );

                    $uploadedCount++;

                } catch (\Exception $e) {
                    $failedPhotos[] = [
                        'name' => $photo->getClientOriginalName(),
                        'error' => $e->getMessage(),
                    ];
                }
            }

            $this->results = [
                'success' => true,
                'failed' => $failedPhotos,
                'count' => $uploadedCount,
                'failed_count' => count($failedPhotos),
            ];

            $this->successMessage = __('photos_uploaded_success', ['count' => $uploadedCount]);

            if (count($failedPhotos) > 0) {
                $this->successMessage .= ' ' . __('files_failed', ['count' => count($failedPhotos)]);
            }

            $this->showSuccessModal = true;
            $this->reset(['photos']);

        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            $this->results = [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }

        $this->isProcessing = false;
    }

    public function closeSuccessModal(): void
    {
        $this->showSuccessModal = false;
        $this->results = [];
    }
};

?>

<div>
    @livewire('frontend.ui.header', ['currentTeam' => $currentTeam])
    @livewire('frontend.islands.filter-bar')

    <div class="max-w-2xl mx-auto py-8 px-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">@lang('upload_photos')</h1>

        <!-- Tab Navigation Component -->
        @livewire('frontend.ui.uploads-tab-navigation')

        @if($errorMessage)
            <div class="mb-4 p-3 bg-red-100 dark:bg-red-900/20 border border-red-400 text-red-700 dark:text-red-400 rounded-lg">
                {{ $errorMessage }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
            <form wire:submit.prevent="save" class="p-6 space-y-5">
                <!-- Album Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('album')</label>

                    <div class="space-y-2">
                        <label class="flex items-center gap-2">
                            <input type="radio" wire:model.live="createNewAlbum" value="0" class="w-4 h-4">
                            <span>@lang('select_existing_album')</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" wire:model.live="createNewAlbum" value="1" class="w-4 h-4">
                            <span>@lang('create_new_album')</span>
                        </label>
                    </div>

                    @if(!$createNewAlbum)
                        <select wire:model="selectedAlbumId" class="mt-3 w-full px-3 py-2 border rounded-lg dark:bg-gray-700">
                            <option value="">@lang('select_album')</option>
                            @foreach($this->userAlbums as $album)
                                <option value="{{ $album->id }}">{{ $album->title }}</option>
                            @endforeach
                        </select>
                        @error('selectedAlbumId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    @else
                        <input type="text" wire:model="newAlbumTitle" placeholder="@lang('album_title')" class="mt-3 w-full px-3 py-2 border rounded-lg dark:bg-gray-700">
                        @error('newAlbumTitle') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    @endif
                </div>

                <!-- Multiple File Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('photos')</label>

                    <div id="dropzone"
                         class="border-2 border-dashed rounded-lg p-8 text-center transition-colors cursor-pointer border-gray-300 dark:border-gray-600 hover:border-stage-500"
                         x-data="{}"
                         x-init="() => {
                             const dropzone = document.getElementById('dropzone');

                             dropzone.addEventListener('dragover', (e) => {
                                 e.preventDefault();
                                 dropzone.classList.add('border-stage-600', 'bg-stage-50', 'dark:bg-stage-900/20');
                             });

                             dropzone.addEventListener('dragleave', (e) => {
                                 e.preventDefault();
                                 dropzone.classList.remove('border-stage-600', 'bg-stage-50', 'dark:bg-stage-900/20');
                             });

                             dropzone.addEventListener('drop', (e) => {
                                 e.preventDefault();
                                 dropzone.classList.remove('border-stage-600', 'bg-stage-50', 'dark:bg-stage-900/20');

                                 const files = Array.from(e.dataTransfer.files);
                                 const imageFiles = files.filter(f => f.type.startsWith('image/'));
                                 if (imageFiles.length > 0) {
                                     for (let file of imageFiles) {
                                         $wire.upload('photos', file);
                                     }
                                 }
                             });
                         }">

                        <div class="text-4xl mb-2">📸</div>
                        <p class="text-gray-600 dark:text-gray-400 mb-2">
                            @lang('drag_drop_photos')
                            <label class="text-stage-600 hover:underline cursor-pointer">
                                @lang('browse')
                                <input type="file" wire:model="photos" multiple accept="image/jpeg,image/png,image/gif,image/webp" class="hidden" id="file-input">
                            </label>
                        </p>
                        <p class="text-xs text-gray-500">@lang('file_types_allowed')</p>
                    </div>

                    @error('photos') <span class="text-red-500 text-sm block mt-1">{{ $message }}</span> @enderror
                    @error('photos.*') <span class="text-red-500 text-sm block mt-1">{{ $message }}</span> @enderror

                    @if(count($photos) > 0)
                        <div class="mt-4 space-y-2 max-h-80 overflow-y-auto">
                            @foreach($photos as $index => $photo)
                                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-200 dark:bg-gray-600 flex-shrink-0">
                                            <img src="{{ $photo->temporaryUrl() }}" alt="@lang('photo_preview')" class="w-full h-full object-cover">
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                {{ $photo->getClientOriginalName() }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ number_format($photo->getSize() / 1024, 1) }} KB
                                            </p>
                                        </div>

                                        <button type="button" wire:click="removePhoto({{ $index }})" class="text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20" aria-label="@lang('remove')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                            @lang('photos_will_use_filenames')
                        </div>
                    @endif
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        wire:loading.attr="disabled"
                        class="w-full py-3 bg-stage-600 text-white rounded-lg hover:bg-stage-700 transition disabled:opacity-50">
                    <span wire:loading.remove>@lang('upload_photos_button', ['count' => count($photos)])</span>
                    <span wire:loading>
                        <svg class="animate-spin h-5 w-5 inline mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        @lang('processing_photos', ['count' => count($photos)])
                    </span>
                </button>
            </form>
        </div>
    </div>

    <!-- Success Modal -->
    @if($showSuccessModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: true }" x-show="open" x-cloak>
            <div class="fixed inset-0 bg-black/50" @click="open = false; $wire.closeSuccessModal()"></div>
            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div class="relative max-w-md w-full bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <div class="text-center">
                        <div class="text-5xl mb-4">✅</div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">@lang('upload_complete')</h3>
                        <p class="text-gray-600 dark:text-gray-400">{{ $successMessage }}</p>

                        @if(isset($results['failed']) && count($results['failed']) > 0)
                            <div class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                <p class="text-red-600 text-sm font-semibold">@lang('failed_uploads')</p>
                                <ul class="text-xs text-red-500 mt-1 max-h-32 overflow-y-auto">
                                    @foreach($results['failed'] as $failed)
                                        <li>{{ $failed['name'] }}: {{ $failed['error'] }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mt-6 flex gap-3">
                            <button @click="open = false; $wire.closeSuccessModal()" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                @lang('close')
                            </button>
                            <a href="{{ route('albums.index') }}" class="flex-1 px-4 py-2 bg-stage-600 text-white rounded-lg hover:bg-stage-700 transition text-center">
                                @lang('view_my_albums')
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @livewire('frontend.ui.footer', ['currentTeam' => $currentTeam])
</div>
