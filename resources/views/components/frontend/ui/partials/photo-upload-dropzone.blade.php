<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('photos')</label>

    <div id="dropzone"
         class="border-2 border-dashed rounded-lg p-8 text-center transition-colors cursor-pointer border-gray-300 dark:border-gray-600 hover:border-stage-500"
         x-data="{}"
         x-init="() => {
             const dropzone = document.getElementById('dropzone');
             const fileInput = document.getElementById('file-input');

             const handleFiles = (files) => {
                 const imageFiles = Array.from(files).filter(f => f.type.startsWith('image/'));
                 if (imageFiles.length > 0) {
                     for (let file of imageFiles) {
                         @this.upload('photos', file);
                     }
                 }
             };

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
                 handleFiles(e.dataTransfer.files);
             });

             fileInput.addEventListener('change', (e) => {
                 handleFiles(e.target.files);
                 fileInput.value = '';
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

    @error('photos') <span class="text-red-500 text-sm block text-center mt-2">{{ $message }}</span> @enderror
    @error('photos.*') <span class="text-red-500 text-sm block text-center mt-2">{{ $message }}</span> @enderror

    @if(count($photos) > 0)
        <div class="mt-4">
            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">@lang('selected_files') ({{ count($photos) }})</h4>
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
</div>
