<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Services\ImageProcessingService;
use App\Models\Album;

new class extends Component {
    use WithFileUploads;

    // Upload type: 'single', 'multiple', or 'zip'
    public $uploadType = 'single';

    // Album data (bound to album-selector)
    public $selectedAlbumId;
    public $createNewAlbum = false;
    public $newAlbumTitle = '';
    public $newAlbumDescription = '';
    public $newAlbumParentId;
    public $newAlbumCategoryId;

    // File data
    public $photos = [];
    public $zipFile;

    // Single photo metadata
    public $photoTitle = '';
    public $photoDescription = '';

    // Upload state
    public $isProcessing = false;
    public $results = [];
    public $showSuccessModal = false;
    public $successMessage = '';
    public $errorMessage = '';

    protected $imageService;

    public function boot(ImageProcessingService $imageService): void
    {
        $this->imageService = $imageService;
    }

    public function mount($uploadType = 'single'): void
    {
        $this->uploadType = $uploadType;
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
            $result = $this->processUploads($album);

            $this->results = $result;
            $this->successMessage = $this->buildSuccessMessage($result);
            $this->showSuccessModal = true;

            $this->resetForm();

        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            $this->results = ['success' => false, 'error' => $e->getMessage()];
        }

        $this->isProcessing = false;
    }

    private function getTargetAlbum()
    {
        if ($this->createNewAlbum) {
            $albumData = [
                'title' => $this->newAlbumTitle,
                'slug' => Str::slug($this->newAlbumTitle) . '-' . uniqid(),
                'description' => $this->newAlbumDescription,
                'photographer_id' => auth()->id(),
                'event_date' => now(),
                'is_published' => false,
            ];

            if ($this->newAlbumParentId) {
                $albumData['parent_id'] = $this->newAlbumParentId;
            }

            $album = Album::create($albumData);

            if ($this->newAlbumCategoryId) {
                $album->categories()->attach($this->newAlbumCategoryId);
            }

            return $album;
        }

        return Album::findOrFail($this->selectedAlbumId);
    }

    private function processUploads($album)
    {
        if ($this->uploadType === 'zip') {
            return $this->imageService->processZipUpload($this->zipFile, $album);
        }

        $uploadedCount = 0;
        $failedPhotos = [];

        foreach ($this->photos as $photo) {
            try {
                $title = $this->uploadType === 'single' ? $this->photoTitle : null;
                $description = $this->uploadType === 'single' ? $this->photoDescription : null;

                $this->imageService->processUpload($photo, $album, $title, $description);
                $uploadedCount++;

            } catch (\Exception $e) {
                $failedPhotos[] = [
                    'name' => $photo->getClientOriginalName(),
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'success' => true,
            'count' => $uploadedCount,
            'failed_count' => count($failedPhotos),
            'failed' => $failedPhotos,
        ];
    }

    private function buildSuccessMessage(array $result)
    {
        if ($this->uploadType === 'zip') {
            $message = ($result['success_count'] ?? 0) . ' ' . __('photos_uploaded_success_partial');
            if (($result['error_count'] ?? 0) > 0) {
                $message .= ' ' . __('files_failed', ['count' => $result['error_count']]);
            }
            return $message;
        }

        $message = __('photos_uploaded_success', ['count' => $result['count']]);
        if (($result['failed_count'] ?? 0) > 0) {
            $message .= ' ' . __('files_failed', ['count' => $result['failed_count']]);
        }
        return $message;
    }

    /**
     * @return 'nullable|string|max:255'[]|'nullable|string|max:5000'[]|'required|array|min:1'[]|'required|exists:albums,id'[]|'required|file|mimes:zip|max:204800'[]|'required|image|max:51200'[]|'required|string|min:3|max:255'[]
     */
    protected function rules(): array
    {
        $rules = [];

        if ($this->uploadType === 'zip') {
            $rules['zipFile'] = 'required|file|mimes:zip|max:204800';
        } else {
            $rules['photos.*'] = 'required|image|max:51200';
            $rules['photos'] = 'required|array|min:1';
        }

        if ($this->createNewAlbum) {
            $rules['newAlbumTitle'] = 'required|string|min:3|max:255';
        } else {
            $rules['selectedAlbumId'] = 'required|exists:albums,id';
        }

        if ($this->uploadType === 'single') {
            $rules['photoTitle'] = 'nullable|string|max:255';
            $rules['photoDescription'] = 'nullable|string|max:5000';
        }

        return $rules;
    }

    public function resetForm(): void
    {
        $this->photos = [];
        $this->zipFile = null;
        $this->photoTitle = '';
        $this->photoDescription = '';
        $this->createNewAlbum = false;
        $this->newAlbumTitle = '';
        $this->newAlbumDescription = '';
        $this->newAlbumParentId = null;
        $this->newAlbumCategoryId = null;
    }

    public function closeSuccessModal(): void
    {
        $this->showSuccessModal = false;
        $this->results = [];
    }
};

?>

<div class="space-y-5">
    <!-- Album Selector -->
    <div>
        @livewire('frontend.ui.album-selector', [
            'selectedAlbumId' => $selectedAlbumId,
        ], key('album-selector-' . $uploadType))
    </div>

    <!-- File Upload Area -->
    @if($uploadType !== 'zip')
        @include('components.frontend.ui.partials.photo-upload-dropzone')
    @else
        @include('components.frontend.ui.partials.zip-upload-dropzone')
    @endif

    <!-- Single Photo Details -->
    @if($uploadType === 'single' && count($photos) === 1)
        @include('components.frontend.ui.partials.photo-details-form')
    @endif

    <!-- Submit Button -->
    <div class="border-t border-gray-200 dark:border-gray-700 p-6 bg-gray-50 dark:bg-gray-900 -mx-6 -mb-6">
        <button type="button"
                wire:click="upload"
                wire:loading.attr="disabled"
                class="w-full px-6 py-3 bg-stage-600 text-white rounded-lg hover:bg-stage-700 transition disabled:opacity-50 flex items-center justify-center gap-2">
            <span wire:loading.remove>@lang('start_upload')</span>
            <span wire:loading>
                <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                @lang('processing')
            </span>
        </button>
    </div>

    <!-- Success Modal -->
    @include('components.frontend.ui.partials.upload-success-modal')
</div>
