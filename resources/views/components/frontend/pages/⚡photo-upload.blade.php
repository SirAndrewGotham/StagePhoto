<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Services\ImageProcessingService;
use App\Services\UnsortedAlbumService;
use App\Models\Album;

new class extends Component {
    use WithFileUploads;

    public $currentTeam = null;

    // Album selection
    public $selectedAlbumId = null;
    public $createNewAlbum = false;
    public $newAlbumTitle = '';

    // Photo data
    public $photo = null;
    public $photoTitle = '';
    public $photoDescription = '';
    public $photoPreview = null;

    // Upload state
    public $isProcessing = false;
    public $successMessage = '';
    public $errorMessage = '';
    public $showSuccess = false;

    protected $imageService;
    protected $unsortedService;

    public function boot(
        ImageProcessingService $imageService,
        UnsortedAlbumService $unsortedService
    ) {
        $this->imageService = $imageService;
        $this->unsortedService = $unsortedService;
    }

    public function mount($currentTeam = null)
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

    public function updatedPhoto()
    {
        if ($this->photo) {
            $this->photoPreview = $this->photo->temporaryUrl();
        }
    }

    public function save()
    {
        $this->validate([
            'photo' => 'required|image|max:51200',
        ]);

        $this->isProcessing = true;
        $this->errorMessage = '';

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

            $processedPhoto = $this->imageService->processUpload(
                $this->photo,
                $album,
                $this->photoTitle ?: null,
                $this->photoDescription ?: null
            );

            $this->successMessage = 'Photo uploaded successfully!';
            $this->showSuccess = true;
            $this->reset(['photo', 'photoTitle', 'photoDescription', 'photoPreview']);

        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }

        $this->isProcessing = false;
    }

    public function closeSuccess()
    {
        $this->showSuccess = false;
        $this->successMessage = '';
    }
};

?>

<div>
    @livewire('frontend.ui.header', ['currentTeam' => $currentTeam])
    @livewire('frontend.islands.filter-bar')

    <div class="max-w-2xl mx-auto py-8 px-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Upload Photos</h1>

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
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Album</label>

                    <div class="space-y-2">
                        <label class="flex items-center gap-2">
                            <input type="radio" wire:model.live="createNewAlbum" value="0" class="w-4 h-4">
                            <span>Select existing album</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" wire:model.live="createNewAlbum" value="1" class="w-4 h-4">
                            <span>Create new album</span>
                        </label>
                    </div>

                    @if(!$createNewAlbum)
                        <select wire:model="selectedAlbumId" class="mt-3 w-full px-3 py-2 border rounded-lg dark:bg-gray-700">
                            <option value="">-- Select an album --</option>
                            @foreach($this->userAlbums as $album)
                                <option value="{{ $album->id }}">{{ $album->title }}</option>
                            @endforeach
                        </select>
                        @error('selectedAlbumId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    @else
                        <input type="text" wire:model="newAlbumTitle" placeholder="Album title" class="mt-3 w-full px-3 py-2 border rounded-lg dark:bg-gray-700">
                        @error('newAlbumTitle') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    @endif
                </div>

                <!-- File Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Photo</label>

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

                                 const files = e.dataTransfer.files;
                                 if (files.length > 0 && files[0].type.startsWith('image/')) {
                                     $wire.upload('photo', files[0]);
                                 }
                             });
                         }">

                        <div class="text-4xl mb-2">📷</div>
                        <p class="text-gray-600 dark:text-gray-400 mb-2">
                            Drag & drop your photo here, or
                            <label class="text-stage-600 hover:underline cursor-pointer">
                                browse
                                <input type="file" wire:model="photo" accept="image/jpeg,image/png,image/gif,image/webp" class="hidden" id="file-input">
                            </label>
                        </p>
                        <p class="text-xs text-gray-500">JPG, PNG, GIF, WebP (max 50MB)</p>
                    </div>

                    @error('photo') <span class="text-red-500 text-sm block mt-1">{{ $message }}</span> @enderror

                    @if($photo)
                        <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center gap-4">
                                <div class="w-20 h-20 rounded-lg overflow-hidden bg-gray-200 dark:bg-gray-600 flex-shrink-0">
                                    @if($photoPreview)
                                        <img src="{{ $photoPreview }}" alt="Preview" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-2xl">📷</div>
                                    @endif
                                </div>

                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $photo->getClientOriginalName() }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ number_format($photo->getSize() / 1024, 1) }} KB
                                    </p>
                                </div>

                                <button type="button" wire:click="$set('photo', null)" class="text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Photo Details -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Photo Details (Optional)</label>
                    <input type="text" wire:model="photoTitle" placeholder="Title" class="w-full px-3 py-2 border rounded-lg mb-2 dark:bg-gray-700">
                    <textarea wire:model="photoDescription" placeholder="Description" rows="3" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700"></textarea>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        wire:loading.attr="disabled"
                        class="w-full py-3 bg-stage-600 text-white rounded-lg hover:bg-stage-700 transition disabled:opacity-50">
                    <span wire:loading.remove>Upload Photo</span>
                    <span wire:loading>
                        <svg class="animate-spin h-5 w-5 inline mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    </span>
                </button>
            </form>
        </div>
    </div>

    <!-- Success Modal -->
    @if($showSuccess)
        <div class="fixed inset-0 z-50 flex items-center justify-center" x-data="{ open: true }" x-show="open" x-cloak>
            <div class="fixed inset-0 bg-black/50" @click="open = false; $wire.closeSuccess()"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4 text-center">
                <div class="text-5xl mb-3">✅</div>
                <h3 class="text-xl font-bold mb-2">Success!</h3>
                <p>{{ $successMessage }}</p>
                <button @click="open = false; $wire.closeSuccess()" class="mt-4 px-4 py-2 bg-stage-600 text-white rounded-lg hover:bg-stage-700">
                    Close
                </button>
            </div>
        </div>
    @endif

    @livewire('frontend.ui.footer', ['currentTeam' => $currentTeam])
</div>
