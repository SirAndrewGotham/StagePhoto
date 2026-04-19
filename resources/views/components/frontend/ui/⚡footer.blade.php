<?php

use Livewire\Component;

new class extends Component {
    public $currentTeam;

    public function mount($currentTeam = null): void
    {
        $this->currentTeam = $currentTeam;
    }

    public function getCurrentYearProperty()
    {
        return now()->year;
    }
};

?>

<footer class="border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 py-8 mt-12">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-sm">
            <div>
                <h4 class="font-semibold mb-3 text-gray-900 dark:text-white">{{ __('album.platform') }}</h4>
                <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('album.submit_work_link') }}</a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('album.for_bands') }}</a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('album.for_theaters') }}</a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('album.photographer_guide') }}</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-3 text-gray-900 dark:text-white">{{ __('album.community') }}</h4>
                <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('album.featured_artists') }}</a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('album.monthly_contest') }}</a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('album.workshops') }}</a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('album.blog') }}</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-3 text-gray-900 dark:text-white">{{ __('album.legal') }}</h4>
                <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('album.privacy_policy') }}</a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('album.terms_of_service') }}</a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('album.copyright') }}</a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('album.cookie_settings') }}</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-3 text-gray-900 dark:text-white">{{ __('album.connect') }}</h4>
                <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('album.telegram') }}</a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('album.vkontakte') }}</a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('album.instagram') }}</a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">{{ __('album.email_support') }}</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-800 flex flex-col md:flex-row items-center justify-between gap-4 text-sm text-gray-500 dark:text-gray-400">
            <p>© 2008-{{ date('Y') }} StagePhoto.ru</p>
            <div class="flex items-center gap-4">
                <span>{{ __('album.made_in') }}</span>
                <button @click="toggleDarkMode()" class="flex items-center gap-1 hover:text-stage-600 transition-colors">
                    <span x-show="!darkMode">{{ __('album.light') }}</span>
                    <span x-show="darkMode" x-cloak>{{ __('album.dark') }}</span>
                </button>
            </div>
        </div>
    </div>
</footer>
