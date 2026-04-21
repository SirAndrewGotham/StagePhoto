<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('zip_archive')</label>

    <div id="dropzone-zip"
         class="border-2 border-dashed rounded-lg p-8 text-center transition-colors cursor-pointer border-gray-300 dark:border-gray-600 hover:border-stage-500"
         x-data="{}"
         x-init="() => {
             const dropzone = document.getElementById('dropzone-zip');
             const zipInput = document.getElementById('zip-input');

             const handleZipFile = (file) => {
                 if (file && file.name.endsWith('.zip')) {
                     @this.set('zipFile', file);
                 } else {
                     alert('@lang('please_drop_zip')');
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
                 const files = Array.from(e.dataTransfer.files);
                 if (files.length > 0) {
                     handleZipFile(files[0]);
                 }
             });

             zipInput.addEventListener('change', (e) => {
                 if (e.target.files.length > 0) {
                     handleZipFile(e.target.files[0]);
                 }
                 zipInput.value = '';
             });
         }">

        <div class="text-4xl mb-2">🗜️</div>
        <p class="text-gray-600 dark:text-gray-400 mb-2">
            @lang('drag_drop_zip')
            <label class="text-stage-600 hover:underline cursor-pointer">
                @lang('browse')
                <input type="file" wire:model="zipFile" accept=".zip" class="hidden" id="zip-input">
            </label>
        </p>
        <p class="text-xs text-gray-500">@lang('zip_file_types_allowed')</p>
    </div>

    @error('zipFile') <span class="text-red-500 text-sm block text-center mt-2">{{ $message }}</span> @enderror

    @if($zipFile)
        <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <p class="text-sm text-gray-600 dark:text-gray-400 text-center">
                @lang('selected'): {{ $zipFile->getClientOriginalName() }} ({{ number_format($zipFile->getSize() / 1024, 1) }} KB)
            </p>
        </div>
    @endif

    <!-- ZIP Info -->
    <div class="mt-4 bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
        <div class="flex items-start gap-3">
            <div class="text-blue-500 text-xl">💡</div>
            <div class="text-sm text-blue-800 dark:text-blue-300">
                <p class="font-medium mb-1">@lang('about_zip_uploads')</p>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    <li>@lang('supports_images')</li>
                    <li>@lang('filenames_as_titles')</li>
                    <li>@lang('subfolders_supported')</li>
                    <li>@lang('exif_auto_extracted')</li>
                    <li>@lang('max_zip_size')</li>
                    <li>@lang('invalid_files_skipped')</li>
                </ul>
            </div>
        </div>
    </div>
</div>
