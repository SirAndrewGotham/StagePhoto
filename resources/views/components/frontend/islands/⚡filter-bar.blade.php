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

        // If translation exists (doesn't return the key itself), use it
        if ($translation !== $key) {
            return $translation;
        }

        // Otherwise return the category name from database
        return $category['name'];
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

            // Add "All" option
            $genreList[] = [
                'slug' => 'all',
                'name' => __('album.all'),
                'icon' => '🎯',
                'type' => null,
            ];

            // Add categories from database
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
            // Fallback if database isn't ready
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
        $this->dispatch('type-changed', type: $type);
        $this->dispatch('genre-changed', genre: 'all');
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
        </div>
    </div>
</div>
