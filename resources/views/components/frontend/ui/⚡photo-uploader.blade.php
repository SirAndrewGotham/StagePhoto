<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Services\ImageProcessingService;
use App\Models\Album;

new class extends Component {
    use WithFileUploads;

    public Album $album;
    public $photos = [];
    public $zipFile;
    public $uploadType = 'single'; // 'single', 'multiple', 'zip'
    public $uploadProgress = [];
    public $isProcessing = false;
    public $results = [];

    protected $rules = [
        'photos.*' => 'image|max:51200', // 50MB max per image
        'zipFile' => 'file|mimes:zip|max:102400', // 100MB max for ZIP
    ];

    protected $imageService;

    public function boot(ImageProcessingService $imageService): void
    {
        $this->imageService = $imageService;
    }

    public function updatedPhotos(): void
    {
        $this->validateOnly('photos');
    }

    public function updatedZipFile(): void
    {
        $this->validateOnly('zipFile');
    }

    public function uploadSingle(): void
    {
        $this->validate();
        $this->isProcessing = true;

        try {
            foreach ($this->photos as $photo) {
                $result = $this->imageService->processUpload($photo, $this->album);
                $this->results[] = [
                    'success' => true,
                    'name' => $photo->getClientOriginalName(),
                    'photo' => $result,
                ];
            }

            $this->photos = [];
            $this->dispatch('photos-uploaded');

        } catch (\Exception $e) {
            $this->results[] = [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }

        $this->isProcessing = false;
    }

    public function uploadZip(): void
    {
        $this->validate();
        $this->isProcessing = true;

        try {
            $result = $this->imageService->processZipUpload($this->zipFile, $this->album);
            $this->results = $result;

            $this->zipFile = null;
            $this->dispatch('photos-uploaded');

        } catch (\Exception $e) {
            $this->results = [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }

        $this->isProcessing = false;
    }

    public function render(): string
    {
        return <<<'HTML'
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Upload Photos</h2>

                <!-- Upload Type Selector -->
                <div class="flex gap-2 mb-4">
                    <button
                        wire:click="$set('uploadType', 'single')"
                        class="px-4 py-2 rounded-lg transition-colors {{ $uploadType === 'single' ? 'bg-stage-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}"
                    >
                        Single Photo
                    </button>
                    <button
                        wire:click="$set('uploadType', 'multiple')"
                        class="px-4 py-2 rounded-lg transition-colors {{ $uploadType === 'multiple' ? 'bg-stage-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}"
                    >
                        Multiple Photos
                    </button>
                    <button
                        wire:click="$set('uploadType', 'zip')"
                        class="px-4 py-2 rounded-lg transition-colors {{ $uploadType === 'zip' ? 'bg-stage-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}"
                    >
                        ZIP Archive
                    </button>
                </div>

                <!-- Single/Multiple Upload -->
                @if($uploadType === 'single' || $uploadType === 'multiple')
                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center">
                        <input
                            type="file"
                            wire:model="photos"
                            {{ $uploadType === 'multiple' ? 'multiple' : '' }}
                            accept="image/jpeg,image/png,image/gif,image/webp"
                            class="hidden"
                            id="file-input"
                        >
                        <label for="file-input" class="cursor-pointer">
                            <div class="text-4xl mb-2">📸</div>
                            <p class="text-gray-600 dark:text-gray-400">
                                Click to select {{ $uploadType === 'multiple' ? 'photos' : 'a photo' }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">JPG, PNG, GIF, WebP (max 50MB)</p>
                        </label>
                    </div>

                    @error('photos.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                    @if(count($photos) > 0)
                        <div class="mt-4">
                            <h4 class="font-semibold mb-2">Selected files ({{ count($photos) }})</h4>
                            <div class="max-h-40 overflow-y-auto space-y-1">
                                @foreach($photos as $index => $photo)
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $photo->getClientOriginalName() }}
                                        ({{ number_format($photo->getSize() / 1024, 1) }} KB)
                                    </div>
                                @endforeach
                            </div>
                            <button
                                wire:click="uploadSingle"
                                wire:loading.attr="disabled"
                                class="mt-4 px-4 py-2 bg-stage-600 text-white rounded-lg hover:bg-stage-700 transition"
                            >
                                <span wire:loading.remove>Upload {{ count($photos) }} file(s)</span>
                                <span wire:loading>Processing...</span>
                            </button>
                        </div>
                    @endif
                @endif

                <!-- ZIP Upload -->
                @if($uploadType === 'zip')
                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center">
                        <input
                            type="file"
                            wire:model="zipFile"
                            accept=".zip"
                            class="hidden"
                            id="zip-input"
                        >
                        <label for="zip-input" class="cursor-pointer">
                            <div class="text-4xl mb-2">🗜️</div>
                            <p class="text-gray-600 dark:text-gray-400">
                                Click to select a ZIP archive
                            </p>
                            <p class="text-xs text-gray-500 mt-1">ZIP with images (max 100MB, 100 images)</p>
                        </label>
                    </div>

                    @error('zipFile') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                    @if($zipFile)
                        <div class="mt-4">
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                Selected: {{ $zipFile->getClientOriginalName() }}
                                ({{ number_format($zipFile->getSize() / 1024, 1) }} KB)
                            </div>
                            <button
                                wire:click="uploadZip"
                                wire:loading.attr="disabled"
                                class="mt-4 px-4 py-2 bg-stage-600 text-white rounded-lg hover:bg-stage-700 transition"
                            >
                                <span wire:loading.remove>Extract & Upload</span>
                                <span wire:loading>Processing ZIP...</span>
                            </button>
                        </div>
                    @endif
                @endif

                <!-- Results -->
                @if(!empty($results))
                    <div class="mt-6 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
                        <h4 class="font-semibold mb-2">Upload Results</h4>
                        @if(isset($results['success_count']))
                            <p class="text-green-600">✓ {{ $results['success_count'] }} photos uploaded successfully</p>
                            @if($results['error_count'] > 0)
                                <p class="text-red-600">✗ {{ $results['error_count'] }} failed</p>
                            @endif
                        @else
                            @foreach($results as $result)
                                @if($result['success'])
                                    <p class="text-green-600 text-sm">✓ {{ $result['name'] }}</p>
                                @else
                                    <p class="text-red-600 text-sm">✗ {{ $result['error'] ?? 'Unknown error' }}</p>
                                @endif
                            @endforeach
                        @endif
                    </div>
                @endif
            </div>
        HTML;
    }
};
?>
