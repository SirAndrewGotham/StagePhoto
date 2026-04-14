<?php

use Livewire\Component;

new class extends Component {
    public $selectedGenre = 'all';
    public $sortBy = 'mostRecent';

    public function selectGenre($genre)
    {
        $this->selectedGenre = $genre;
        $this->dispatch('genre-changed', genre: $genre);
    }

    public function updateSort($value)
    {
        $this->sortBy = $value;
        $this->dispatch('sort-changed', sort: $value);
    }

    public function getGenresProperty()
    {
        return [
            'all' => 'Все / All / Ĉiuj',
            'rock' => 'Рок / Rock / Roko',
            'metal' => 'Метал / Metal / Metalo',
            'theater' => 'Театр / Theater / Teatro',
            'festivals' => 'Фестивали / Festivals / Festivaloj',
            'jazz' => 'Джаз / Jazz / Ĵazo',
            'classical' => 'Классика / Classical / Klasika',
            'electronic' => 'Электроника / Electronic / Elektronika',
            'folk' => 'Фолк / Folk / Folko',
        ];
    }
};

?>

<div class="sticky top-16 z-40 bg-white/95 dark:bg-gray-900/95 backdrop-blur-sm border-b border-gray-200 dark:border-gray-800 py-3">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center gap-3">
            <div class="filter-pills flex gap-2 overflow-x-auto pb-1 flex-1 min-w-0">
                @foreach($this->genres as $key => $label)
                    <button
                        wire:click="selectGenre('{{ $key }}')"
                        class="px-3 lg:px-4 py-1.5 rounded-full text-xs lg:text-sm font-medium whitespace-nowrap transition-colors
                            {{ $selectedGenre === $key
                                ? 'bg-stage-600 text-white shadow-md'
                                : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'
                            }}"
                    >
                        {{ explode(' / ', $label)[0] }}
                    </button>
                @endforeach
            </div>

            <select
                wire:change="updateSort($event.target.value)"
                class="px-3 py-1.5 text-xs lg:text-sm border border-gray-300 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-stage-500 focus:border-transparent min-w-[120px] lg:min-w-[140px]"
            >
                <option value="mostRecent" {{ $sortBy === 'mostRecent' ? 'selected' : '' }} x-text="t('mostRecent')"></option>
                <option value="mostViewed" {{ $sortBy === 'mostViewed' ? 'selected' : '' }} x-text="t('mostViewed')"></option>
                <option value="topRated" {{ $sortBy === 'topRated' ? 'selected' : '' }} x-text="t('topRated')"></option>
                <option value="newPhotographers" {{ $sortBy === 'newPhotographers' ? 'selected' : '' }} x-text="t('newPhotographers')"></option>
            </select>

            <div class="md:hidden flex-1 min-w-[100px]">
                <input
                    type="search"
                    x-bind:placeholder="t('search')"
                    class="w-full pl-9 pr-4 py-1.5 text-sm border border-gray-300 dark:border-gray-700 rounded-xl bg-gray-100 dark:bg-gray-800 focus:ring-2 focus:ring-stage-500"
                >
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        window.addEventListener('language-changed', () => {
            Livewire.dispatch('$refresh');
        });
    });
</script>
