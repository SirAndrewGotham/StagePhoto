<?php

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use App\Services\CategoryService;

new class extends Component {
    #[Url(as: 'genre', history: true)]
    public $selectedGenre = 'all';

    #[Url(as: 'sort', history: true)]
    public $sortBy = 'mostRecent';

    #[Url(as: 'type', history: true)]
    public $selectedType = 'all';

    #[Url(as: 'year', history: true)]
    public $selectedYear;

    #[Url(as: 'q', history: true)]
    public $search = '';

    protected $categoryService;

    public function boot(CategoryService $categoryService): void
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Helper function to translate category names
     * Uses translation key "album.categories.{slug}" with fallback to database name
     */
    private function transCategory(array $category)
    {
        $key = "album.categories.{$category['slug']}";
        $translation = __($key);

        if ($translation !== $key) {
            return $translation;
        }

        return $category['name'];
    }

    public function getAvailableYearsProperty(): array
    {
        $years = range(date('Y') - 10, date('Y'));
        return array_reverse($years);
    }

    #[Computed]
    public function genres()
    {
        try {
            $categories = $this->categoryService->getAllCategories();

            if ($this->selectedType !== 'all') {
                $categories = array_filter($categories, fn(array $category) => $category['type'] === $this->selectedType);
            }

            $genreList = [];

            $genreList[] = [
                'slug' => 'all',
                'name' => __('album.all'),
                'icon' => '🎯',
                'type' => null,
            ];

            foreach ($categories as $category) {
                $genreList[] = [
                    'slug' => $category['slug'],
                    'name' => $this->transCategory($category),
                    'icon' => $category['icon'],
                    'type' => $category['type'],
                ];
            }

            return $genreList;

        } catch (\Exception) {
            return [
                ['slug' => 'all', 'name' => __('album.all'), 'icon' => '🎯'],
                ['slug' => 'rock', 'name' => __('album.rock'), 'icon' => '🎸'],
                ['slug' => 'metal', 'name' => __('album.metal'), 'icon' => '🤘'],
                ['slug' => 'theater', 'name' => __('album.theater'), 'icon' => '🎭'],
                ['slug' => 'festivals', 'name' => __('album.festivals'), 'icon' => '🎪'],
                ['slug' => 'jazz', 'name' => __('album.jazz'), 'icon' => '🎷'],
                ['slug' => 'classical', 'name' => __('album.classical'), 'icon' => '🎻'],
                ['slug' => 'electronic', 'name' => __('album.electronic'), 'icon' => '🎧'],
                ['slug' => 'folk', 'name' => __('album.folk'), 'icon' => '🪕'],
            ];
        }
    }

    #[Computed]
    public function categoryTypes(): array
    {
        return [
            ['value' => 'all', 'label' => __('album.type_all'), 'icon' => '🎯'],
            ['value' => 'music', 'label' => __('album.type_music'), 'icon' => '🎸'],
            ['value' => 'theater', 'label' => __('album.type_theater'), 'icon' => '🎭'],
        ];
    }

    public function selectGenre($genre): void
    {
        $this->selectedGenre = $genre;
        $this->dispatch('genre-changed', genre: $genre);
    }

    public function selectType($type): void
    {
        $this->selectedType = $type;
        $this->selectedGenre = 'all';
        $this->selectedYear = null;
        $this->dispatch('type-changed', type: $type);
        $this->dispatch('genre-changed', genre: 'all');
        $this->dispatch('year-changed', year: null);
    }

    public function selectYear($year): void
    {
        $this->selectedYear = $year;
        $this->dispatch('year-changed', year: $year);
    }

    public function clearYear(): void
    {
        $this->selectedYear = null;
        $this->dispatch('year-changed', year: null);
    }

    public function updateSort($value): void
    {
        $this->sortBy = $value;
        $this->dispatch('sort-changed', sort: $value);
    }

    public function updatedSearch(): void
    {
        $this->dispatch('search-changed', search: $this->search);
    }

    public function clearAllFilters(): void
    {
        $this->selectedGenre = 'all';
        $this->selectedType = 'all';
        $this->selectedYear = null;
        $this->sortBy = 'mostRecent';
        $this->search = '';

        $this->dispatch('genre-changed', genre: 'all');
        $this->dispatch('type-changed', type: 'all');
        $this->dispatch('year-changed', year: null);
        $this->dispatch('sort-changed', sort: 'mostRecent');
        $this->dispatch('search-changed', search: '');
    }

    public function getActiveFiltersCountProperty(): int
    {
        $count = 0;
        if ($this->selectedGenre !== 'all') $count++;
        if ($this->selectedType !== 'all') $count++;
        if ($this->selectedYear !== null) $count++;
        if ($this->sortBy !== 'mostRecent') $count++;
        if (!empty($this->search)) $count++;
        return $count;
    }
};

?>

<div class="sticky top-16 z-40 bg-white/95 dark:bg-gray-900/95 backdrop-blur-sm border-b border-gray-200 dark:border-gray-800 py-3">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center gap-3">
            <!-- Type Tabs (Music / Theater / All) -->
            <div class="flex gap-1 bg-gray-100 dark:bg-gray-800 rounded-xl p-1">
                @foreach($this->categoryTypes as $type)
                    <button
                        wire:click="selectType('{{ $type['value'] }}')"
                        class="px-3 py-1.5 rounded-lg text-xs lg:text-sm font-medium transition-all whitespace-nowrap
                            {{ $selectedType === $type['value']
                                ? 'bg-stage-600 text-white shadow-md'
                                : 'text-gray-700 dark:text-gray-300 hover:bg-white/50 dark:hover:bg-gray-700/50'
                            }}"
                    >
                        <span class="mr-1">{{ $type['icon'] }}</span>
                        <span>{{ $type['label'] }}</span>
                    </button>
                @endforeach
            </div>

            <!-- Genre Pills -->
            <div class="filter-pills flex gap-2 overflow-x-auto pb-1 flex-1 min-w-0">
                @foreach($this->genres as $genre)
                    <button
                        wire:click="selectGenre('{{ $genre['slug'] }}')"
                        class="px-3 lg:px-4 py-1.5 rounded-full text-xs lg:text-sm font-medium whitespace-nowrap transition-colors flex items-center gap-1
                            {{ $selectedGenre === $genre['slug']
                                ? 'bg-stage-600 text-white shadow-md'
                                : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'
                            }}"
                    >
                        @if($genre['icon'])
                            <span>{{ $genre['icon'] }}</span>
                        @endif
                        <span>{{ $genre['name'] }}</span>
                    </button>
                @endforeach
            </div>

            <!-- Year Filter Dropdown -->
            <div class="relative">
                <select
                    wire:change="selectYear($event.target.value)"
                    class="px-3 py-1.5 text-xs lg:text-sm border border-gray-300 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-stage-500 focus:border-transparent"
                >
                    <option value="">📅 All Years</option>
                    @foreach($this->availableYears as $year)
                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
                @if($selectedYear)
                    <button
                        wire:click="clearYear"
                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500"
                    >
                        ✕
                    </button>
                @endif
            </div>

            <!-- Sort Dropdown -->
            <select
                wire:change="updateSort($event.target.value)"
                class="px-3 py-1.5 text-xs lg:text-sm border border-gray-300 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-stage-500 focus:border-transparent min-w-[140px]"
            >
                <option value="mostRecent" {{ $sortBy === 'mostRecent' ? 'selected' : '' }}>{{ __('album.most_recent') }}</option>
                <option value="mostViewed" {{ $sortBy === 'mostViewed' ? 'selected' : '' }}>{{ __('album.most_viewed') }}</option>
                <option value="topRated" {{ $sortBy === 'topRated' ? 'selected' : '' }}>{{ __('album.top_rated') }}</option>
                <option value="newPhotographers" {{ $sortBy === 'newPhotographers' ? 'selected' : '' }}>{{ __('album.new_photographers') }}</option>
            </select>

            <!-- Mobile Search -->
            <div class="md:hidden flex-1 min-w-[100px]">
                <input
                    type="search"
                    placeholder="{{ __('album.search') }}"
                    wire:model.live.debounce.300ms="search"
                    class="w-full pl-9 pr-4 py-1.5 text-sm border border-gray-300 dark:border-gray-700 rounded-xl bg-gray-100 dark:bg-gray-800 focus:ring-2 focus:ring-stage-500"
                >
            </div>

            <!-- Active Filters Indicator & Clear Button -->
            @if($this->activeFiltersCount > 0)
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500">
                        {{ $this->activeFiltersCount }} active filter(s)
                    </span>
                    <button
                        wire:click="clearAllFilters"
                        class="px-2 py-1 text-xs rounded-lg bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-900/50 transition"
                    >
                        ✕ Clear all
                    </button>
                </div>
            @endif
        </div>

        <!-- Desktop Search -->
        <div class="hidden md:block mt-3">
            <div class="relative max-w-md">
                <input
                    type="search"
                    placeholder="{{ __('album.search') }}"
                    wire:model.live.debounce.300ms="search"
                    class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-700 rounded-xl bg-gray-100 dark:bg-gray-800 focus:ring-2 focus:ring-stage-500 focus:border-transparent"
                >
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                @if(!empty($search))
                    <button
                        wire:click="$set('search', '')"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500"
                    >
                        ✕
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
