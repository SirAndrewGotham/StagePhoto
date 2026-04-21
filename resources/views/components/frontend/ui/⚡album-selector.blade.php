<?php

use Livewire\Component;
use App\Models\Album;
use App\Models\Category;

new class extends Component {
    // Public properties for binding
    public $selectedAlbumId;
    public $createNewAlbum = false;
    public $newAlbumTitle = '';
    public $newAlbumDescription = '';
    public $newAlbumParentId;
    public $newAlbumCategoryId;

    // Data properties
    public $albumTree = [];
    public $categoriesByType = [];

    // Events
    public $listeners = ['resetAlbumSelector' => 'resetForm'];

    public function mount($selectedAlbumId = null): void
    {
        $this->selectedAlbumId = $selectedAlbumId;
        $this->loadAlbumTree();
        $this->loadCategoriesByType();
    }

    public function loadAlbumTree(): void
    {
        $albums = Album::where('photographer_id', auth()->id())
            ->where('is_unsorted', false)
            ->orderBy('title')
            ->get();

        $this->albumTree = $this->buildAlbumTree($albums);
    }

    /**
     * @return mixed[]
     */
    private function buildAlbumTree($albums, $parentId = null, int|float $depth = 0): array
    {
        $tree = [];

        foreach ($albums as $album) {
            if ($album->parent_id == $parentId) {
                $tree[] = [
                    'id' => $album->id,
                    'title' => $album->title,
                    'depth' => $depth,
                ];

                $children = $this->buildAlbumTree($albums, $album->id, $depth + 1);
                $tree = array_merge($tree, $children);
            }
        }

        return $tree;
    }

    public function loadCategoriesByType(): void
    {
        $categories = Category::active()
            ->orderBy('type')
            ->orderBy('sort_order')
            ->get();

        $this->categoriesByType = [
            'music' => [],
            'theater' => []
        ];

        foreach ($categories as $category) {
            $type = $category->type === 'theater' ? 'theater' : 'music';
            $this->categoriesByType[$type][] = [
                'id' => $category->id,
                'icon' => $category->icon,
                'name' => $category->name,
                'slug' => $category->slug,
            ];
        }
    }

    public function getUserAlbumsProperty()
    {
        return Album::where('photographer_id', auth()->id())
            ->where('is_unsorted', false)
            ->orderBy('title')
            ->get();
    }

    public function getTargetAlbumData(): ?array
    {
        if (!$this->createNewAlbum) {
            return null;
        }

        $albumData = [
            'title' => $this->newAlbumTitle,
            'description' => $this->newAlbumDescription,
        ];

        if ($this->newAlbumParentId) {
            $albumData['parent_id'] = $this->newAlbumParentId;
        }

        if ($this->newAlbumCategoryId) {
            $albumData['category_id'] = $this->newAlbumCategoryId;
        }

        return $albumData;
    }

    public function resetForm(): void
    {
        $this->createNewAlbum = false;
        $this->newAlbumTitle = '';
        $this->newAlbumDescription = '';
        $this->newAlbumParentId = null;
        $this->newAlbumCategoryId = null;
    }

    public function updatedCreateNewAlbum(): void
    {
        if (!$this->createNewAlbum) {
            $this->resetForm();
        }
    }
};

?>

<div>
    <!-- Album Selection -->
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('album')</label>

        <div class="space-y-2">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" wire:model.live="createNewAlbum" value="0" class="w-4 h-4 text-stage-600">
                <span class="text-gray-900 dark:text-white">@lang('select_existing_album')</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" wire:model.live="createNewAlbum" value="1" class="w-4 h-4 text-stage-600">
                <span class="text-gray-900 dark:text-white">@lang('create_new_album')</span>
            </label>
        </div>

        @if(!$createNewAlbum)
            <div class="mt-3">
                <select wire:model="selectedAlbumId" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 text-sm font-mono">
                    <option value="">-- @lang('select_album') --</option>
                    @foreach($albumTree as $album)
                        <option value="{{ $album['id'] }}">
                            @for($i = 0; $i < $album['depth']; $i++)
                                @if($i == $album['depth'] - 1)
                                    └─
                                @else
                                    &nbsp;&nbsp;&nbsp;
                                @endif
                            @endfor
                            @if($album['depth'] == 0)
                                📁
                            @endif
                            {{ $album['title'] }}
                        </option>
                    @endforeach
                </select>
                @error('selectedAlbumId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                <div class="mt-2 text-xs text-gray-500 flex items-center gap-3">
                    <span class="inline-flex items-center gap-1">📁 <span>@lang('root_album')</span></span>
                    <span class="inline-flex items-center gap-1">└─ <span>@lang('sub_album_levels')</span></span>
                    <span class="inline-flex items-center gap-1">&nbsp;&nbsp;&nbsp;└─ <span>@lang('sub_album_multi_level')</span></span>
                </div>
            </div>
        @else
            <div class="mt-3 space-y-3">
                <!-- Parent Album Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">@lang('parent_album')</label>
                    <select wire:model="newAlbumParentId" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 text-sm font-mono">
                        <option value="">-- @lang('top_level_album') --</option>
                        @foreach($albumTree as $album)
                            <option value="{{ $album['id'] }}">
                                @for($i = 0; $i < $album['depth']; $i++)
                                    @if($i == $album['depth'] - 1)
                                        └─
                                    @else
                                        &nbsp;&nbsp;&nbsp;
                                    @endif
                                @endfor
                                @if($album['depth'] == 0)
                                    📁
                                @endif
                                {{ $album['title'] }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1 flex items-center gap-2">
                        <span>💡</span>
                        <span>@lang('parent_album_hint')</span>
                    </p>
                </div>

                <!-- Album Title -->
                <input type="text" wire:model="newAlbumTitle" placeholder="@lang('album_title')" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700">
                @error('newAlbumTitle') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                <!-- Album Description -->
                <textarea wire:model="newAlbumDescription" placeholder="@lang('album_description')" rows="2" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700"></textarea>

                <!-- Category Selection (only for new albums) -->
                @if(!empty($categoriesByType['music']) || !empty($categoriesByType['theater']))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">@lang('category_optional')</label>
                        <select wire:model="newAlbumCategoryId" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700">
                            <option value="">-- @lang('select_category') --</option>

                            @if(!empty($categoriesByType['music']))
                                <optgroup label="@lang('music_categories')">
                                    @foreach($categoriesByType['music'] as $category)
                                        <option value="{{ $category['id'] }}">{{ $category['icon'] }} {{ $category['name'] }}</option>
                                    @endforeach
                                </optgroup>
                            @endif

                            @if(!empty($categoriesByType['theater']))
                                <optgroup label="@lang('theater_categories')">
                                    @foreach($categoriesByType['theater'] as $category)
                                        <option value="{{ $category['id'] }}">{{ $category['icon'] }} {{ $category['name'] }}</option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </select>
                        <p class="text-xs text-gray-500 mt-1">@lang('category_hint')</p>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
