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

    // ZIP file
    public $zipFile = null;

    // Upload state
    public $isProcessing = false;
    public $results = [];
    public $showSuccessModal = false;
    public $successMessage = '';
    public $errorMessage = '';
    public $extractedCount = 0;
    public $failedCount = 0;
    public $failedFiles = [];

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

    public function save()
    {
        $this->validate([
            'zipFile' => 'required|file|mimes:zip|max:204800', // 200MB max
        ]);

        $this->isProcessing = true;
        $this->errorMessage = '';
        $this->results = [];
        $this->failedFiles = [];

        try {
            // Get or create album
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

            // Process the ZIP file
            $result = $this->imageService->processZipUpload($this->zipFile, $album);

            $this->extractedCount = $result['success_count'] ?? 0;
            $this->failedCount = $result['error_count'] ?? 0;
            $this->failedFiles = $result['errors'] ?? [];

            $this->successMessage = $this->extractedCount . ' photo(s) uploaded successfully!';

            if ($this->failedCount > 0) {
                $this->successMessage .= ' ' . $this->failedCount . ' file(s) failed.';
            }

            $this->showSuccessModal = true;
            $this->reset(['zipFile']);

        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            $this->results = [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }

        $this->isProcessing = false;
    }

    public function closeSuccessModal()
    {
        $this->showSuccessModal = false;
        $this->results = [];
        $this->failedFiles = [];
    }
};

?>

<div>
    @livewire('frontend.ui.header', ['currentTeam' => $currentTeam])
    @livewire('frontend.islands.filter-bar')

    <div class="max-w-2xl mx-auto py-8 px-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Upload ZIP Archive</h1>

        @if($errorMessage)
            <div class="mb-4 p-3 bg-red-100 dark:bg-red-900/20 border border-red-400 text-red-700 dark:text-red-400 rounded-lg">
                {{ $errorMessage }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
            <form wire:submit.prevent="save" class="p-6 space-y-5">
                <!-- Tab Navigation Component -->
                @livewire('frontend.ui.uploads-tab-navigation')

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
                                <option value="{{ $album->id }}">{{ $album->title }} ({{ $album->photo_count }} photos)</option>
                            @endforeach
                        </select>
                        @error('selectedAlbumId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    @else
                        <div class="mt-3 space-y-3">
                            <input type="text" wire:model="newAlbumTitle" placeholder="Album title *" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700">
                            @error('newAlbumTitle') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>

                <!-- ZIP File Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ZIP Archive</label>

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
                                 if (files.length > 0 && files[0].name.endsWith('.zip')) {
                                     $wire.upload('zipFile', files[0]);
                                 } else {
                                     alert('Please drop a ZIP file');
                                 }
                             });
                         }">

                        <div class="text-4xl mb-2">🗜️</div>
                        <p class="text-gray-600 dark:text-gray-400 mb-2">
                            Drag & drop your ZIP archive here, or
                            <label class="text-stage-600 hover:underline cursor-pointer">
                                browse
                                <input type="file" wire:model="zipFile" accept=".zip" class="hidden" id="zip-input">
                            </label>
                        </p>
                        <p class="text-xs text-gray-500">ZIP with images (max 200MB, supports JPG, PNG, GIF, WebP)</p>
                    </div>

                    @error('zipFile') <span class="text-red-500 text-sm block mt-1">{{ $message }}</span> @enderror

                    @if($zipFile)
                        <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium">{{ $zipFile->getClientOriginalName() }}</p>
                                <p class="text-xs text-gray-500">{{ number_format($zipFile->getSize() / 1024, 1) }} KB</p>
                            </div>
                            <button type="button" wire:click="$set('zipFile', null)" class="text-red-500 hover:text-red-700">
                                ✕
                            </button>
                        </div>
                    @endif
                </div>

                <!-- ZIP Info -->
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                    <div class="flex items-start gap-3">
                        <div class="text-blue-500 text-xl">💡</div>
                        <div class="text-sm text-blue-800 dark:text-blue-300">
                            <p class="font-medium mb-1">About ZIP uploads:</p>
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                <li>Supports JPG, PNG, GIF, WebP images</li>
                                <li>Images will use filenames as titles</li>
                                <li>Subfolders are supported and flattened</li>
                                <li>EXIF data is automatically extracted</li>
                                <li>Maximum ZIP size: 200MB</li>
                                <li>Invalid files are skipped with error reporting</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        wire:loading.attr="disabled"
                        class="w-full py-3 bg-stage-600 text-white rounded-lg hover:bg-stage-700 transition disabled:opacity-50">
                    <span wire:loading.remove>Extract & Upload ZIP</span>
                    <span wire:loading>
                        <svg class="animate-spin h-5 w-5 inline mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing ZIP...
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
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Extraction Complete!</h3>
                        <p class="text-gray-600 dark:text-gray-400">{{ $successMessage }}</p>

                        @if($failedCount > 0)
                            <div class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg text-left">
                                <p class="text-yellow-600 text-sm font-semibold mb-2">Failed files ({{ $failedCount }}):</p>
                                <ul class="text-xs text-yellow-500 space-y-1 max-h-32 overflow-y-auto">
                                    @foreach($failedFiles as $failed)
                                        <li><strong>{{ $failed['file'] }}</strong>: {{ $failed['error'] }}</li>
                                    @endforeach
                                </ul>
                                <p class="text-xs text-gray-500 mt-2">
                                    💡 Note: macOS metadata files (._filename) and hidden files are automatically skipped.
                                </p>
                            </div>
                        @endif

                        <div class="mt-6 flex gap-3">
                            <button @click="open = false; $wire.closeSuccessModal()" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                Close
                            </button>
                            <a href="{{ route('albums.index') }}" class="flex-1 px-4 py-2 bg-stage-600 text-white rounded-lg hover:bg-stage-700 transition text-center">
                                View My Albums
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @livewire('frontend.ui.footer', ['currentTeam' => $currentTeam])
</div>
