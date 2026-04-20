<?php

use Livewire\Component;
use Livewire\Attributes\Title;

new class extends Component {
    #[Title('Photographer Guide - StagePhoto.ru')]
    public function render()
    {
        $locale = app()->getLocale();

        $contentView = "components.frontend.pages.legal.photographer-guide.⚡{$locale}";

        if (!view()->exists($contentView)) {
            $contentView = "components.frontend.pages.legal.photographer-guide.⚡ru";
        }

        return view('components.frontend.pages.legal.⚡photographer-guide', [
            'contentView' => $contentView
        ]);
    }
};

?>

<div>
    @livewire('frontend.ui.header', ['currentTeam' => null])

    <div class="max-w-5xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
            <div class="relative h-64 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-cyan-600 opacity-90"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center text-white">
                        <div class="text-5xl mb-4">📸</div>
                        <h1 class="text-4xl font-bold mb-2">@lang('photographer_guide')</h1>
                        <p class="text-lg opacity-90">@lang('photographer_guide_subtitle')</p>
                    </div>
                </div>
            </div>

            <div class="p-6 md:p-8 prose prose-gray dark:prose-invert max-w-none">
                @include($contentView)
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 p-6 bg-gray-50 dark:bg-gray-900 text-center">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">@lang('ready_to_start_shooting')</h3>
                <a href="{{ route('register') }}" class="inline-block px-6 py-3 bg-stage-600 hover:bg-stage-700 text-white font-medium rounded-lg transition-colors shadow-md hover:shadow-lg">
                    @lang('join_stagephoto_today')
                </a>
            </div>
        </div>
    </div>

    @livewire('frontend.ui.footer', ['currentTeam' => null])
</div>
