<?php

use Livewire\Component;
use Livewire\Attributes\Title;

new class extends Component {
    #[Title('Copyright Information - StagePhoto.ru')]
    public function render()
    {
        $locale = app()->getLocale();

        // Build the view path for language-specific content
        $contentView = "components.frontend.pages.legal.copyright.⚡{$locale}";

        // Check if the language-specific view exists
        if (!view()->exists($contentView)) {
            // Fallback to Russian
            $contentView = "components.frontend.pages.legal.copyright.⚡ru";
        }

        return view('components.frontend.pages.legal.⚡copyright', [
            'contentView' => $contentView
        ]);
    }
};

?>

<div>
    @livewire('frontend.ui.header', ['currentTeam' => null])

    <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
            <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">@lang('copyright')</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">@lang('last_updated'): April 20, 2026</p>
            </div>

            <div class="p-6 prose prose-gray dark:prose-invert max-w-none">
                @include($contentView)
            </div>
        </div>
    </div>

    @livewire('frontend.ui.footer', ['currentTeam' => null])
</div>
