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
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 text-sm">
            <div>
                <h4 class="font-semibold mb-3 text-gray-900 dark:text-white">@lang('album.platform')</h4>
                <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                    <li><a href="{{ route('photo.upload') }}" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">@lang('album.submit_work_link')</a></li>
                    <li><a href="{{ route('for-bands') }}" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">@lang('album.for_bands')</a></li>
                    <li><a href="{{ route('for-theaters') }}" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">@lang('album.for_theaters')</a></li>
                    <li><a href="{{ route('photographer-guide') }}" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">@lang('album.photographer_guide')</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-3 text-gray-900 dark:text-white">@lang('album.community')</h4>
                <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">@lang('album.featured_artists')</a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">@lang('album.monthly_contest')</a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">@lang('album.workshops')</a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">@lang('album.blog')</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-3 text-gray-900 dark:text-white">@lang('album.legal')</h4>
                <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                    <li><a href="{{ route('terms') }}" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">@lang('album.terms_of_service')</a></li>
                    <li><a href="{{ route('privacy') }}" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">@lang('album.privacy_policy')</a></li>
                    <li><a href="{{ route('guidelines') }}" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">@lang('album.community_guidelines')</a></li>
                    <li><a href="{{ route('copyright') }}" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">@lang('album.copyright')</a></li>
                    <li><a href="{{ route('cookies') }}" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">@lang('album.cookie_settings')</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-3 text-gray-900 dark:text-white">@lang('album.connect')</h4>
                <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">@lang('album.telegram')</a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">@lang('album.vkontakte')</a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">@lang('album.instagram')</a></li>
                    <li><a href="#" class="hover:text-stage-600 dark:hover:text-stage-400 transition-colors">@lang('album.email_support')</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-800 flex flex-col md:flex-row items-center justify-between gap-4 text-sm text-gray-500 dark:text-gray-400">
            <p>© 2008-{{ date('Y') }} StagePhoto.ru</p>
            <div class="flex items-center gap-4">
                <span>@lang('album.made_in')</span>
                <button @click="toggleDarkMode()" class="flex items-center gap-1 hover:text-stage-600 transition-colors">
                    <span x-show="!darkMode">@lang('album.light')</span>
                    <span x-show="darkMode" x-cloak>@lang('album.dark')</span>
                </button>
            </div>
        </div>
    </div>
</footer>
