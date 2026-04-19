<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Services\ImageProcessingService;
use App\Services\UnsortedAlbumService;
use App\Models\Album;
use App\Models\Category;

new class extends Component {
    use WithFileUploads;

    public $currentTeam;

    // Album selection
    public $selectedAlbumId;
    public $createNewAlbum = false;
    public $newAlbumTitle = '';
    public $newAlbumDescription = '';

    // Category selection (only for new albums)
    public $selectedCategoryId;

    // Upload files - using a simple array
    public $photos = [];
    public $zipFile;
    public $uploadType = 'single';

    // Single photo metadata
    public $photoTitle = '';
    public $photoDescription = '';

    // Upload state
    public $isProcessing = false;
    public $results = [];
    public $showSuccessModal = false;
    public $successMessage = '';
    public $errorMessage = '';

    // Data properties
    public $userAlbums = [];
    public $categoriesList = [];

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
        $this->loadUserAlbums();
        $this->loadCategories();
    }

    public function loadUserAlbums(): void
    {
        $albums = Album::where('photographer_id', auth()->id())
            ->where('is_unsorted', false)
            ->orderBy('created_at', 'desc')
            ->get();

        $this->userAlbums = [];
        foreach ($albums as $album) {
            $this->userAlbums[] = [
                'id' => $album->id,
                'title' => $album->title,
                'photo_count' => $album->photo_count,
            ];
        }
    }

    public function loadCategories(): void
    {
        $categories = Category::active()
            ->orderBy('sort_order')
            ->get();

        $this->categoriesList = [];
        foreach ($categories as $category) {
            $this->categoriesList[] = [
                'id' => $category->id,
                'icon' => $category->icon,
                'name' => $category->name,
            ];
        }
    }

    /**
     * @return 'nullable|string|max:255'[]|'nullable|string|max:5000'[]|'required|array|min:1'[]|'required|file|mimes:zip|max:102400'[]|'required|image|max:51200'[]|'required|string|min:3|max:255'[]
     */
    protected function rules(): array
    {
        $rules = [];

        if ($this->uploadType === 'zip') {
            $rules['zipFile'] = 'required|file|mimes:zip|max:102400';
        } else {
            $rules['photos.*'] = 'required|image|max:51200';
            $rules['photos'] = 'required|array|min:1';
        }

        if ($this->createNewAlbum) {
            $rules['newAlbumTitle'] = 'required|string|min:3|max:255';
        }

        if ($this->uploadType === 'single') {
            $rules['photoTitle'] = 'nullable|string|max:255';
            $rules['photoDescription'] = 'nullable|string|max:5000';
        }

        return $rules;
    }

    public function removePhoto($index): void
    {
        unset($this->photos[$index]);
        $this->photos = array_values($this->photos);
    }

    public function upload(): void
    {
        $this->validate();
        $this->isProcessing = true;
        $this->errorMessage = '';
        $this->results = [];

        try {
            $album = $this->getTargetAlbum();

            if ($this->uploadType === 'zip') {
                $result = $this->imageService->processZipUpload($this->zipFile, $album);
                $this->zipFile = null;
                $this->results = $result;
                $this->successMessage = ($result['success_count'] ?? 0) . ' photos uploaded successfully!';

                if (($result['error_count'] ?? 0) > 0) {
                    $this->successMessage .= ' ' . $result['error_count'] . ' files failed.';
                }
            } else {
                $uploadedCount = 0;
                $failedPhotos = [];

                foreach ($this->photos as $photo) {
                    try {
                        $title = $this->uploadType === 'single' ? $this->photoTitle : null;
                        $description = $this->uploadType === 'single' ? $this->photoDescription : null;

                        $processedPhoto = $this->imageService->processUpload($photo, $album, $title, $description);

                        if ($this->createNewAlbum && $this->selectedCategoryId) {
                            $processedPhoto->categories()->attach($this->selectedCategoryId);
                        }

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

                $this->successMessage = $uploadedCount . ' photo(s) uploaded successfully!';

                if (count($failedPhotos) > 0) {
                    $this->successMessage .= ' ' . count($failedPhotos) . ' failed.';
                }
            }

            $this->showSuccessModal = true;
            $this->resetForm();
            $this->loadUserAlbums();

        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            $this->results = [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }

        $this->isProcessing = false;
    }

    private function getTargetAlbum(): Album
    {
        if ($this->createNewAlbum) {
            return Album::create([
                'title' => $this->newAlbumTitle,
                'slug' => Str::slug($this->newAlbumTitle) . '-' . uniqid(),
                'description' => $this->newAlbumDescription,
                'photographer_id' => auth()->id(),
                'event_date' => now(),
                'is_published' => false,
            ]);
        }

        if ($this->selectedAlbumId) {
            return Album::findOrFail($this->selectedAlbumId);
        }

        return $this->unsortedService->getOrCreateUnsortedAlbum(auth()->user());
    }

    private function resetForm(): void
    {
        $this->photos = [];
        $this->zipFile = null;
        $this->photoTitle = '';
        $this->photoDescription = '';
        $this->selectedCategoryId = null;

        if ($this->createNewAlbum) {
            $this->newAlbumTitle = '';
            $this->newAlbumDescription = '';
            $this->createNewAlbum = false;
        }
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

    <div class="max-w-4xl mx-auto py-8 px-4">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Upload Photos</h1>

        @if($errorMessage)
            <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/20 border border-red-400 text-red-700 dark:text-red-400 rounded-lg">
                {{ $errorMessage }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <!-- Upload Type Selector -->
            <div class="border-b border-gray-200 dark:border-gray-700 p-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Upload Type</label>
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="button"
                            wire:click="$set('uploadType', 'single')"
                            class="flex-1 px-4 py-3 rounded-lg border-2 transition-all {{ $uploadType === 'single' ? 'border-stage-600 bg-stage-50 dark:bg-stage-900/20' : 'border-gray-300 dark:border-gray-600' }}">
                        <div class="text-2xl mb-1">📷</div>
                        <div class="font-medium">Single Photo</div>
                        <div class="text-xs text-gray-500">Upload one photo with details</div>
                    </button>
                    <button type="button"
                            wire:click="$set('uploadType', 'multiple')"
                            class="flex-1 px-4 py-3 rounded-lg border-2 transition-all {{ $uploadType === 'multiple' ? 'border-stage-600 bg-stage-50 dark:bg-stage-900/20' : 'border-gray-300 dark:border-gray-600' }}">
                        <div class="text-2xl mb-1">📸📸</div>
                        <div class="font-medium">Multiple Photos</div>
                        <div class="text-xs text-gray-500">Upload several photos at once</div>
                    </button>
                    <button type="button"
                            wire:click="$set('uploadType', 'zip')"
                            class="flex-1 px-4 py-3 rounded-lg border-2 transition-all {{ $uploadType === 'zip' ? 'border-stage-600 bg-stage-50 dark:bg-stage-900/20' : 'border-gray-300 dark:border-gray-600' }}">
                        <div class="text-2xl mb-1">🗜️</div>
                        <div class="font-medium">ZIP Archive</div>
                        <div class="text-xs text-gray-500">Upload a ZIP file with photos</div>
                    </button>
                </div>
            </div>

            <!-- Album Selection -->
            <div class="border-b border-gray-200 dark:border-gray-700 p-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Album</label>

                <div class="space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="radio" wire:model.live="createNewAlbum" value="0" class="w-4 h-4 text-stage-600">
                        <span class="text-gray-900 dark:text-white">Select existing album</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="radio" wire:model.live="createNewAlbum" value="1" class="w-4 h-4 text-stage-600">
                        <span class="text-gray-900 dark:text-white">Create new album</span>
                    </label>
                </div>

                <!-- Existing Album Select -->
                <div wire:key="existing-album-{{ $createNewAlbum }}">
                    @if(!$createNewAlbum)
                        <div class="mt-3">
                            <select wire:model="selectedAlbumId" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-stage-500 dark:bg-gray-700">
                                <option value="">-- Select an album --</option>
                                @foreach($userAlbums as $album)
                                    <option value="{{ $album['id'] }}">{{ $album['title'] }} ({{ $album['photo_count'] }} photos)</option>
                                @endforeach
                            </select>
                            @error('selectedAlbumId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>

                <!-- New Album Form -->
                <div wire:key="new-album-{{ $createNewAlbum }}">
                    @if($createNewAlbum)
                        <div class="mt-3 space-y-3">
                            <input type="text" wire:model="newAlbumTitle" placeholder="Album title *" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-stage-500 dark:bg-gray-700">
                            @error('newAlbumTitle') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            <textarea wire:model="newAlbumDescription" placeholder="Album description (optional)" rows="2" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-stage-500 dark:bg-gray-700"></textarea>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Category Selection (Only when creating new album) -->
            @if($createNewAlbum)
                <div class="border-b border-gray-200 dark:border-gray-700 p-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category (Optional for new album)</label>
                    <select wire:model="selectedCategoryId" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-stage-500 dark:bg-gray-700">
                        <option value="">-- Select category --</option>
                        @foreach($categoriesList as $category)
                            <option value="{{ $category['id'] }}">{{ $category['icon'] }} {{ $category['name'] }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Photos in this album will be tagged with this category. Can be changed later.</p>
                </div>
            @endif

            <!-- File Upload Area - Simple Working Version -->
            <div class="p-6">
                @if($uploadType !== 'zip')
                    <div class="text-center">
                        <label class="inline-block bg-stage-600 hover:bg-stage-700 text-white font-medium px-6 py-3 rounded-lg cursor-pointer transition-colors">
                            Select Photos
                            <input type="file" wire:model="photos" multiple accept="image/jpeg,image/png,image/gif,image/webp" class="hidden">
                        </label>
                        <p class="text-xs text-gray-500 mt-2">JPG, PNG, GIF, WebP (max 50MB each)</p>
                    </div>

                    @error('photos') <span class="text-red-500 text-sm block text-center mt-2">{{ $message }}</span> @enderror

                    @if(count($photos) > 0)
                        <div class="mt-4">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Selected files ({{ count($photos) }})</h4>
                            <div class="max-h-40 overflow-y-auto space-y-1 bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                @foreach($photos as $index => $photo)
                                    <div class="text-sm text-gray-600 dark:text-gray-400 flex justify-between items-center">
                                        <span class="truncate flex-1">{{ $photo->getClientOriginalName() }}</span>
                                        <span class="mx-2">{{ number_format($photo->getSize() / 1024, 1) }} KB</span>
                                        <button type="button" wire:click="removePhoto({{ $index }})" class="text-red-500 hover:text-red-700 ml-2">✕</button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-center">
                        <label class="inline-block bg-stage-600 hover:bg-stage-700 text-white font-medium px-6 py-3 rounded-lg cursor-pointer transition-colors">
                            Select ZIP File
                            <input type="file" wire:model="zipFile" accept=".zip" class="hidden">
                        </label>
                        <p class="text-xs text-gray-500 mt-2">ZIP with images (max 100MB, 100 images)</p>
                    </div>

                    @error('zipFile') <span class="text-red-500 text-sm block text-center mt-2">{{ $message }}</span> @enderror

                    @if($zipFile)
                        <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 text-center">
                                Selected: {{ $zipFile->getClientOriginalName() }} ({{ number_format($zipFile->getSize() / 1024, 1) }} KB)
                            </p>
                        </div>
                    @endif
                @endif
            </div>

            <!-- Single Photo Details -->
            @if($uploadType === 'single' && count($photos) === 1)
                <div class="border-t border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Photo Details</h3>

                    <div class="space-y-3">
                        <input type="text" wire:model="photoTitle" placeholder="Photo title (optional)" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-stage-500 dark:bg-gray-700">
                        <textarea wire:model="photoDescription" placeholder="Photo description (optional)" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-stage-500 dark:bg-gray-700"></textarea>
                    </div>
                </div>
            @endif

            <!-- Submit Button -->
            <div class="border-t border-gray-200 dark:border-gray-700 p-6 bg-gray-50 dark:bg-gray-900">
                <button type="button"
                        wire:click="upload"
                        wire:loading.attr="disabled"
                        class="w-full px-6 py-3 bg-stage-600 text-white rounded-lg hover:bg-stage-700 transition disabled:opacity-50 flex items-center justify-center gap-2">
                    <span wire:loading.remove>Start Upload</span>
                    <span wire:loading>
                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    </span>
                </button>
            </div>
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
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Upload Complete!</h3>
                        <p class="text-gray-600 dark:text-gray-400">{{ $successMessage }}</p>

                        @if(isset($results['failed']) && count($results['failed']) > 0)
                            <div class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                <p class="text-red-600 text-sm font-semibold">Failed uploads:</p>
                                <ul class="text-xs text-red-500 mt-1 max-h-32 overflow-y-auto">
                                    @foreach($results['failed'] as $failed)
                                        <li>{{ $failed['name'] }}: {{ $failed['error'] }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(isset($results['errors']) && count($results['errors']) > 0)
                            <div class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                <p class="text-red-600 text-sm font-semibold">ZIP extraction errors:</p>
                                <ul class="text-xs text-red-500 mt-1 max-h-32 overflow-y-auto">
                                    @foreach($results['errors'] as $error)
                                        <li>{{ $error['file'] }}: {{ $error['error'] }}</li>
                                    @endforeach
                                </ul>
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
    @livewire('frontend.ui.request-modal')
</div>
