{{-- 🔝 Header --}}
<header class="sticky top-0 z-50 w-full bg-white/90 dark:bg-stage-900/90 backdrop-blur-sm border-b border-gray-200 dark:border-stage-800">
    <div class="w-full px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between">
        <a href="/" class="flex items-center gap-2">
            <span class="text-2xl font-bold bg-gradient-to-r from-stage-500 to-stage-600 bg-clip-text text-transparent">StagePhoto.ru</span>
        </a>
        <div class="flex items-center gap-2 sm:gap-3">
            {{-- Language Switcher --}}
            <div class="flex items-center bg-gray-100 dark:bg-stage-800 rounded-lg p-0.5">
                <button class="px-2.5 py-1 text-xs font-medium rounded-md bg-stage-600 text-white">RU</button>
                <button class="px-2.5 py-1 text-xs font-medium rounded-md text-gray-600 dark:text-gray-300 hover:bg-white/50">EN</button>
                <button class="px-2.5 py-1 text-xs font-medium rounded-md text-gray-600 dark:text-gray-300 hover:bg-white/50">EO</button>
            </div>
            {{-- Dark Toggle --}}
            <button @click="$dispatch('toggle-dark')" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-stage-800 transition" aria-label="Toggle dark mode">🌙</button>
        </div>
    </div>
</header>
