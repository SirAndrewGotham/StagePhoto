<?php

use Livewire\Component;

new class extends Component {
    //
};

?>

<footer class="border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 py-8 mt-12">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-sm">
            <div>
                <h4 class="font-semibold mb-3 text-gray-900 dark:text-white" x-text="t('platform')"></h4>
                <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors" x-text="t('submitWorkLink')"></a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors" x-text="t('forBands')"></a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors" x-text="t('forTheaters')"></a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors" x-text="t('photographerGuide')"></a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-3 text-gray-900 dark:text-white" x-text="t('community')"></h4>
                <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors" x-text="t('featuredArtists')"></a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors" x-text="t('monthlyContest')"></a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors" x-text="t('workshops')"></a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors" x-text="t('blog')"></a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-3 text-gray-900 dark:text-white" x-text="t('legal')"></h4>
                <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors" x-text="t('privacyPolicy')"></a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors" x-text="t('termsOfService')"></a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors" x-text="t('copyright')"></a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors" x-text="t('cookieSettings')"></a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-3 text-gray-900 dark:text-white" x-text="t('connect')"></h4>
                <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors" x-text="t('telegram')"></a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors" x-text="t('vkontakte')"></a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors" x-text="t('instagram')"></a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors" x-text="t('emailSupport')"></a></li>
                </ul>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-800 flex flex-col md:flex-row items-center justify-between gap-4 text-sm text-gray-500 dark:text-gray-400">
            <p>© 2025 StagePhoto.ru</p>
            <div class="flex items-center gap-4">
                <span x-text="t('madeIn')"></span>
                <button @click="toggleDarkMode()" class="flex items-center gap-1 hover:text-stage-600 transition-colors">
                    <span x-show="!darkMode" x-text="t('light')"></span>
                    <span x-show="darkMode" x-cloak x-text="t('dark')"></span>
                </button>
            </div>
        </div>
    </div>
</footer>
