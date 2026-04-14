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
                <h4 class="font-semibold mb-3 text-gray-900 dark:text-white">{{ __('platform') }}</h4>
                <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('submit_work_link') }}</a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('for_bands') }}</a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('for_theaters') }}</a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('photographer_guide') }}</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-3 text-gray-900 dark:text-white">{{ __('community') }}</h4>
                <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('featured_artists') }}</a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('monthly_contest') }}</a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('workshops') }}</a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('blog') }}</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-3 text-gray-900 dark:text-white">{{ __('legal') }}</h4>
                <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('privacy_policy') }}</a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('terms_of_service') }}</a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('copyright') }}</a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('cookie_settings') }}</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-3 text-gray-900 dark:text-white">{{ __('connect') }}</h4>
                <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('telegram') }}</a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('vkontakte') }}</a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('instagram') }}</a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('email_support') }}</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-800 flex flex-col md:flex-row items-center justify-between gap-4 text-sm text-gray-500 dark:text-gray-400">
            <p>© 2025 StagePhoto.ru</p>
            <div class="flex items-center gap-4">
                <span>{{ __('made_in') }}</span>
                <button @click="toggleDarkMode()" class="flex items-center gap-1 hover:text-stage-600 transition-colors">
                    <span x-show="!darkMode">{{ __('light') }}</span>
                    <span x-show="darkMode" x-cloak>{{ __('dark') }}</span>
                </button>
            </div>
        </div>
    </div>
</footer>
