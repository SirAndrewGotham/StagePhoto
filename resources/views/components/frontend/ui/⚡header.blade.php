<?php

use Livewire\Component;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public $currentTeam;

    public function mount($currentTeam = null): void
    {
        $this->currentTeam = $currentTeam;
    }

    public function switchLanguage($locale): void
    {
        $supportedLocales = ['ru', 'en', 'eo'];

        if (in_array($locale, $supportedLocales)) {
            // Set cookie (1 year)
            cookie()->queue('language', $locale, 60 * 24 * 365);

            // Set session
            session(['locale' => $locale]);

            // Set application locale
            app()->setLocale($locale);
        }

        // Force a full page reload to apply the language to all components
        $this->redirect(request()->header('Referer', '/'));
    }

    public function getCurrentLocaleProperty()
    {
        return app()->getLocale();
    }

    public function getUserProperty()
    {
        return Auth::user();
    }

    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        $this->redirect('/');
    }
};

?>

<header class="sticky top-0 h-16 z-50 bg-white/95 dark:bg-gray-900/95 backdrop-blur-sm border-b border-gray-200 dark:border-gray-800">
    <div class="px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <a href="/" class="flex items-center gap-2 group">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-stage-500 to-orange-600 flex items-center justify-center text-white font-bold text-lg shadow-lg group-hover:shadow-orange-500/30 transition-shadow">
                S
            </div>
            <span class="font-bold text-xl tracking-tight">StagePhoto<span class="text-stage-500">.ru</span></span>
        </a>

        <div class="flex items-center gap-2 sm:gap-3">
            <!-- Desktop Search -->
            <div class="hidden md:block relative">
                <input
                    type="search"
                    placeholder="@lang('search')"
                    class="pl-10 pr-4 py-2 w-64 lg:w-72 text-sm border border-gray-300 dark:border-gray-700 rounded-xl bg-gray-100 dark:bg-gray-800 focus:ring-2 focus:ring-stage-500 focus:border-transparent transition-all"
                >
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <!-- Language Switcher -->
            <div class="flex items-center bg-gray-100 dark:bg-gray-800 rounded-xl p-1 gap-1">
                <button
                    wire:click="switchLanguage('ru')"
                    class="lang-btn px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-all
                        {{ $this->currentLocale === 'ru' ? 'active bg-stage-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-white/50' }}"
                    title="@lang('russian')"
                >
                    RU
                </button>
                <button
                    wire:click="switchLanguage('en')"
                    class="lang-btn px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-all
                        {{ $this->currentLocale === 'en' ? 'active bg-stage-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-white/50' }}"
                    title="@lang('english')"
                >
                    EN
                </button>
                <button
                    wire:click="switchLanguage('eo')"
                    class="lang-btn px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-all
                        {{ $this->currentLocale === 'eo' ? 'active bg-stage-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-white/50' }}"
                    title="@lang('esperanto')"
                >
                    EO
                </button>
            </div>

            <!-- Dark Mode Toggle -->
            <button
                @click="toggleDarkMode()"
                class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                :aria-label="darkMode ? '@lang('light')' : '@lang('dark')'"
            >
                <svg x-show="!darkMode" class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                </svg>
                <svg x-show="darkMode" x-cloak class="w-5 h-5 text-indigo-300" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                </svg>
            </button>

            <!-- Auth Section -->
            @auth
                <!-- When logged in - User menu -->
                <div class="hidden sm:flex items-center gap-2" x-data="{ open: false }">
                    <a href="{{ route('photo.upload') }}"
                       class="px-3 lg:px-4 py-2 text-sm font-medium text-white bg-stage-600 hover:bg-stage-700 rounded-xl transition-colors shadow-md hover:shadow-lg">
                        @lang('submit_work')
                    </a>

                    <div class="relative">
                        <button
                            @click="open = !open"
                            class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                        >
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-stage-500 to-orange-600 flex items-center justify-center text-white font-semibold text-sm">
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                            </div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 hidden lg:inline">
                                {{ Auth::user()->name ?? __('my_account') }}
                            </span>
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div
                            x-show="open"
                            @click.away="open = false"
                            x-cloak
                            class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50"
                        >
                            <a href="{{ route('albums.index') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                @lang('my_albums')
                            </a>
                            <a href="{{ route('photo.upload') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                @lang('upload_photos')
                            </a>
                            <a href="{{ route('trash.manager') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                @lang('trash')
                            </a>
                            <hr class="my-1 border-gray-200 dark:border-gray-700">
                            <button
                                wire:click="logout"
                                class="w-full text-left block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700"
                            >
                                @lang('sign_out')
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mobile menu button (logged in) -->
                <button class="md:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            @else
                <!-- When logged out - Sign In and Submit Work buttons -->
                <div class="hidden sm:flex items-center gap-2">
                    <a href="{{ route('login') }}"
                       class="px-3 lg:px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-stage-600 dark:hover:text-stage-400 transition-colors">
                        @lang('sign_in')
                    </a>
                    <a href="{{ route('photo.upload') }}"
                       class="px-3 lg:px-4 py-2 text-sm font-medium text-white bg-stage-600 hover:bg-stage-700 rounded-xl transition-colors shadow-md hover:shadow-lg">
                        @lang('submit_work')
                    </a>
                </div>

                <!-- Mobile menu button (logged out) -->
                <button class="md:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            @endauth
        </div>
    </div>
</header>
