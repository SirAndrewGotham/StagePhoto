<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Title;
use App\Models\Album;

new class extends Component {
    use WithPagination;

    #[Title('Albums - StagePhoto.ru')]

    // Filter properties (synced with URL)
    #[Url(as: 'genre', history: true)]
    public $genreFilter = 'all';

    #[Url(as: 'type', history: true)]
    public $typeFilter = 'all';

    #[Url(as: 'year', history: true)]
    public $yearFilter;

    #[Url(as: 'sort', history: true)]
    public $sortBy = 'mostRecent';

    #[Url(as: 'q', history: true)]
    public $search = '';

    // Events to listen from filter-bar
    protected $listeners = [
        'genre-changed' => 'updateGenre',
        'type-changed' => 'updateType',
        'year-changed' => 'updateYear',
        'sort-changed' => 'updateSort',
        'search-changed' => 'updateSearch',
    ];

    public function updateGenre($genre): void
    {
        $this->genreFilter = $genre;
        $this->resetPage();
    }

    public function updateType($type): void
    {
        $this->typeFilter = $type;
        $this->genreFilter = 'all';
        $this->yearFilter = null;
        $this->resetPage();
    }

    public function updateYear($year): void
    {
        $this->yearFilter = $year;
        $this->resetPage();
    }

    public function updateSort($sort): void
    {
        $this->sortBy = $sort;
        $this->resetPage();
    }

    public function updateSearch($search): void
    {
        $this->search = $search;
        $this->resetPage();
    }

    public function getAlbumsProperty()
    {
        $query = Album::query()
            ->where('is_published', true)
            ->where('is_unsorted', false)
            ->with('photographer', 'categories');

        // Apply type filter (music/theater)
        if ($this->typeFilter !== 'all') {
            $query->whereHas('categories', function($q) {
                $q->where('type', $this->typeFilter);
            });
        }

        // Apply genre filter (specific category)
        if ($this->genreFilter !== 'all') {
            $query->whereHas('categories', function($q) {
                $q->where('slug', $this->genreFilter);
            });
        }

        // Apply year filter
        if ($this->yearFilter) {
            $query->whereYear('event_date', $this->yearFilter);
        }

        // Apply search
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('venue', 'like', '%' . $this->search . '%')
                    ->orWhereHas('photographer', function($sub) {
                        $sub->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Apply sorting
        match ($this->sortBy) {
            'mostViewed' => $query->orderBy('views', 'desc'),
            'topRated' => $query->orderBy('rating', 'desc'),
            'newPhotographers' => $query->orderBy('created_at', 'desc'),
            // mostRecent
            default => $query->orderBy('event_date', 'desc'),
        };

        return $query->paginate(24);
    }

    public function render()
    {
        return view('components.frontend.pages.⚡albums-index', [
            'albums' => $this->albums,
        ]);
    }
};

?>

<div>
    @livewire('frontend.ui.header', ['currentTeam' => null])
    @livewire('frontend.islands.filter-bar')

    <!-- Back to Entity Link - Shows only when filtering by entity-related tags -->
    <div x-data="{ returnToEntity: null }"
         x-init="returnToEntity = sessionStorage.getItem('returnToEntity');
                  if (returnToEntity) sessionStorage.removeItem('returnToEntity')">

        @if($genreFilter !== 'all' || $typeFilter !== 'all' || $yearFilter || !empty($search))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-sm text-gray-600 dark:text-gray-400">🔍 @lang('filtering_by'):</span>

                            @if($typeFilter !== 'all')
                                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full bg-stage-100 dark:bg-stage-900/30 text-stage-700 dark:text-stage-300">
                                    🏷️ {{ ucfirst($typeFilter) }}
                                    <button wire:click="updateType('all')" class="hover:text-red-500 ml-1">✕</button>
                                </span>
                            @endif

                            @if($genreFilter !== 'all')
                                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full bg-stage-100 dark:bg-stage-900/30 text-stage-700 dark:text-stage-300">
                                    🎸 {{ ucfirst($genreFilter) }}
                                    <button wire:click="updateGenre('all')" class="hover:text-red-500 ml-1">✕</button>
                                </span>
                            @endif

                            @if($yearFilter)
                                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full bg-stage-100 dark:bg-stage-900/30 text-stage-700 dark:text-stage-300">
                                    📅 {{ $yearFilter }}
                                    <button wire:click="updateYear(null)" class="hover:text-red-500 ml-1">✕</button>
                                </span>
                            @endif

                            @if(!empty($search))
                                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full bg-stage-100 dark:bg-stage-900/30 text-stage-700 dark:text-stage-300">
                                    🔍 {{ $search }}
                                    <button wire:click="updateSearch('')" class="hover:text-red-500 ml-1">✕</button>
                                </span>
                            @endif
                        </div>

                        <!-- Link back to the entity page if we came from one -->
                        <a x-show="returnToEntity"
                           :href="'/persona/' + returnToEntity"
                           class="text-sm text-stage-600 hover:text-stage-700 flex items-center gap-1">
                            ← @lang('back_to_entity')
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Album Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($albums->count() > 0)
            <div class="masonry-grid">
                @foreach($albums as $album)
                    @livewire('frontend.islands.album-card', ['album' => $album], key('album-' . $album->id))
                @endforeach
            </div>

            <div class="mt-8">
                {{ $albums->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-6xl mb-4">📭</div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">@lang('no_albums_found')</h3>
                <p class="text-gray-600 dark:text-gray-400">@lang('try_adjusting_filters')</p>
                <button wire:click="updateGenre('all'); updateType('all'); updateYear(null); updateSearch('')"
                        class="mt-4 px-4 py-2 bg-stage-600 text-white rounded-lg hover:bg-stage-700 transition">
                    @lang('clear_all_filters')
                </button>
            </div>
        @endif
    </div>

    @livewire('frontend.ui.footer', ['currentTeam' => null])
</div>
