<?php

use Livewire\Component;
use Livewire\Attributes\Title;

new class extends Component {
    #[Title('Cookie Settings - StagePhoto.ru')]
    public function render()
    {
        $locale = app()->getLocale();

        // Build the view path for language-specific content
        $contentView = "components.frontend.pages.legal.cookies.⚡{$locale}";

        // Check if the language-specific view exists
        if (!view()->exists($contentView)) {
            // Fallback to Russian
            $contentView = "components.frontend.pages.legal.cookies.⚡ru";
        }

        return view('components.frontend.pages.legal.⚡cookies', [
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
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">@lang('cookie_settings')</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">@lang('last_updated'): April 20, 2026</p>
            </div>

            <div class="p-6 prose prose-gray dark:prose-invert max-w-none">
                @include($contentView)
            </div>

            <!-- Interactive Cookie Settings Panel -->
            <div class="border-t border-gray-200 dark:border-gray-700 p-6 bg-gray-50 dark:bg-gray-900">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">@lang('manage_cookie_preferences')</h2>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-white">@lang('essential_cookies')</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">@lang('essential_cookies_desc')</p>
                        </div>
                        <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-sm rounded-full">@lang('always_active')</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-white">@lang('preference_cookies')</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">@lang('preference_cookies_desc')</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="preferenceCookies" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-stage-300 dark:peer-focus:ring-stage-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-stage-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-white">@lang('analytics_cookies')</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">@lang('analytics_cookies_desc')</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="analyticsCookies" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-stage-300 dark:peer-focus:ring-stage-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-stage-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-white">@lang('marketing_cookies')</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">@lang('marketing_cookies_desc')</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="marketingCookies" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-stage-300 dark:peer-focus:ring-stage-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-stage-600"></div>
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button class="px-4 py-2 bg-stage-600 hover:bg-stage-700 text-white rounded-lg transition" onclick="saveCookiePreferences()">@lang('save_preferences')</button>
                    <button class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition" onclick="acceptAllCookies()">@lang('accept_all')</button>
                    <button class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition" onclick="rejectAllCookies()">@lang('reject_all')</button>
                </div>
            </div>
        </div>
    </div>

    @livewire('frontend.ui.footer', ['currentTeam' => null])
</div>

<script>
    function saveCookiePreferences() {
        const preferences = {
            essential: true,
            preference: document.getElementById('preferenceCookies').checked,
            analytics: document.getElementById('analyticsCookies').checked,
            marketing: document.getElementById('marketingCookies').checked
        };
        localStorage.setItem('cookiePreferences', JSON.stringify(preferences));
        alert('Cookie preferences saved!');
    }

    function acceptAllCookies() {
        document.getElementById('preferenceCookies').checked = true;
        document.getElementById('analyticsCookies').checked = true;
        document.getElementById('marketingCookies').checked = true;
        saveCookiePreferences();
    }

    function rejectAllCookies() {
        document.getElementById('preferenceCookies').checked = false;
        document.getElementById('analyticsCookies').checked = false;
        document.getElementById('marketingCookies').checked = false;
        saveCookiePreferences();
    }
</script>
