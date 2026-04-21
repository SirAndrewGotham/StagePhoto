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

                    @if(isset($results['errors']) && count($results['errors']) > 0)
                        <div class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                            <p class="text-red-600 text-sm font-semibold">@lang('zip_extraction_errors')</p>
                            <ul class="text-xs text-red-500 mt-1 max-h-32 overflow-y-auto">
                                @foreach($results['errors'] as $error)
                                    <li>{{ $error['file'] }}: {{ $error['error'] }}</li>
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
