<main class="w-full px-4 sm:px-6 lg:px-8 py-6">
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4 sm:gap-5 lg:gap-6">

        <x-frontend.ui.album-card />

    </div>

    {{-- Pagination / Load More --}}
    <div class="flex justify-center py-8">
        <button class="px-6 py-2.5 text-sm font-medium bg-gray-100 dark:bg-stage-800 hover:bg-gray-200 dark:hover:bg-stage-700 text-gray-800 dark:text-gray-200 rounded-lg transition">
            Загрузить ещё (1,248)
        </button>
    </div>
</main>
