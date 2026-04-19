<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Album;

new class extends Component {
    use WithPagination;

    public $currentTeam = null;
    public $search = '';
    public $sortBy = 'latest';
    public $viewMode = 'grid';
    public $showOnlyMine = false; // For photographers to see their own albums

    public function mount($currentTeam = null)
    {
        $this->currentTeam = $currentTeam;
    }

    public function getAlbumsProperty()
    {
        $query = Album::query();

        // If photographer wants to see only their albums
        if ($this->showOnlyMine && auth()->check()) {
            $query->where('photographer_id', auth()->id());
        } else {
            // Public view - only show published albums
            $query->where('is_published', true)
                ->where('status', 'published');
        }

        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        switch ($this->sortBy) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'most_photos':
                $query->orderBy('photo_count', 'desc');
                break;
            case 'most_views':
                $query->orderBy('views', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        return $query->paginate(12);
    }

    public function getUnsortedAlbumProperty()
    {
        if (!auth()->check()) return null;

        return Album::where('photographer_id', auth()->id())
            ->where('is_unsorted', true)
            ->first();
    }

    public function getPhotographerStatsProperty()
    {
        if (!auth()->check()) return null;

        return [
            'total' => Album::where('photographer_id', auth()->id())->count(),
            'published' => Album::where('photographer_id', auth()->id())->where('is_published', true)->count(),
            'pending' => Album::where('photographer_id', auth()->id())->where('status', 'pending')->count(),
        ];
    }

    // Photographer actions
    public function deleteAlbum($albumId)
    {
        if (!auth()->check()) return;

        $album = Album::findOrFail($albumId);

        // Only the owner can delete
        if ($album->photographer_id !== auth()->id()) {
            return;
        }

        $album->delete();
        $this->dispatch('album-deleted');
        session()->flash('message', 'Album moved to trash');
    }

    public function publishAlbum($albumId)
    {
        if (!auth()->check()) return;

        $album = Album::findOrFail($albumId);

        if ($album->photographer_id !== auth()->id()) {
            return;
        }

        // Check if album needs admin approval
        if ($album->status === 'pending') {
            session()->flash('warning', 'Album is pending admin approval. You will be notified once approved.');
            return;
        }

        $album->update(['is_published' => true]);
        session()->flash('message', 'Album published successfully');
    }

    public function unpublishAlbum($albumId)
    {
        if (!auth()->check()) return;

        $album = Album::findOrFail($albumId);

        if ($album->photographer_id !== auth()->id()) {
            return;
        }

        $album->update(['is_published' => false]);
        session()->flash('message', 'Album unpublished');
    }

    public function getStatusBadge($status)
    {
        return match($status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-blue-100 text-blue-800',
            'published' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'blocked' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
};

?>

<div>
    @livewire('frontend.ui.header', ['currentTeam' => $currentTeam])
    @livewire('frontend.islands.filter-bar')

    <div class="max-w-7xl mx-auto py-8 px-4">
        <!-- Header with Photographer Tools -->
        <div class="flex flex-wrap justify-between items-center mb-6 gap-4">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ auth()->check() && $showOnlyMine ? 'My Albums' : 'Albums' }}
            </h1>

            @auth
                <div class="flex gap-3">
                    @if(!$showOnlyMine)
                        <button wire:click="$set('showOnlyMine', true)"
                                class="px-4 py-2 bg-stage-600 text-white rounded-lg hover:bg-stage-700 transition">
                            My Albums
                        </button>
                    @else
                        <button wire:click="$set('showOnlyMine', false)"
                                class="px-4 py-2 border border-stage-600 text-stage-600 rounded-lg hover:bg-stage-50 transition">
                            Browse All
                        </button>
                    @endif

                    <a href="{{ route('photo.upload') }}" class="px-4 py-2 bg-stage-600 text-white rounded-lg hover:bg-stage-700 transition">
                        + Upload Photos
                    </a>
                </div>
            @endauth
        </div>

        @if(session()->has('message'))
            <div class="mb-4 p-3 bg-green-100 dark:bg-green-900/20 border border-green-400 text-green-700 dark:text-green-400 rounded-lg">
                {{ session('message') }}
            </div>
        @endif

        @if(session()->has('warning'))
            <div class="mb-4 p-3 bg-yellow-100 dark:bg-yellow-900/20 border border-yellow-400 text-yellow-700 dark:text-yellow-400 rounded-lg">
                {{ session('warning') }}
            </div>
        @endif

        <!-- Photographer Stats (only when viewing own albums) -->
        @auth
            @if($showOnlyMine && $this->photographerStats)
                <div class="mb-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->photographerStats['total'] }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Total Albums</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                        <div class="text-2xl font-bold text-green-600">{{ $this->photographerStats['published'] }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Published</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                        <div class="text-2xl font-bold text-yellow-600">{{ $this->photographerStats['pending'] }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Pending Approval</div>
                    </div>
                </div>
            @endif
        @endauth

        <!-- Unsorted Album Card (photographer only) -->
        @auth
            @if($showOnlyMine && $this->unsortedAlbum)
                <div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div class="flex items-center gap-3">
                            <div class="text-3xl">📁</div>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">Unsorted Album</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $this->unsortedAlbum->photo_count }} photos awaiting organization
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('album.show', $this->unsortedAlbum->slug) }}" class="px-3 py-1 text-sm bg-stage-600 text-white rounded-lg hover:bg-stage-700 transition">
                            View & Organize
                        </a>
                    </div>
                </div>
            @endif
        @endauth

        <!-- Filters -->
        <div class="mb-6 flex flex-col sm:flex-row gap-3 justify-between">
            <div class="relative">
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Search albums..."
                       class="pl-10 pr-4 py-2 border rounded-lg w-64 dark:bg-gray-700">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <div class="flex gap-2">
                <select wire:model.live="sortBy" class="px-3 py-2 border rounded-lg dark:bg-gray-700">
                    <option value="latest">Latest</option>
                    <option value="oldest">Oldest</option>
                    <option value="most_photos">Most Photos</option>
                    <option value="most_views">Most Views</option>
                </select>

                <div class="flex gap-1 border rounded-lg overflow-hidden">
                    <button wire:click="$set('viewMode', 'grid')"
                            class="px-3 py-2 {{ $viewMode === 'grid' ? 'bg-stage-600 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                    </button>
                    <button wire:click="$set('viewMode', 'list')"
                            class="px-3 py-2 {{ $viewMode === 'list' ? 'bg-stage-600 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Albums Grid -->
        @if($viewMode === 'grid')
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($this->albums as $album)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition overflow-hidden">
                        <a href="{{ route('album.show', $album->slug) }}">
                            <div class="aspect-video bg-gray-200 dark:bg-gray-700 overflow-hidden">
                                @if($album->cover_image_square)
                                    <img src="{{ $album->cover_image_square }}" alt="{{ $album->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-4xl">📷</div>
                                @endif
                            </div>
                        </a>
                        <div class="p-4">
                            <a href="{{ route('album.show', $album->slug) }}">
                                <h3 class="font-semibold text-gray-900 dark:text-white hover:text-stage-600 transition truncate">
                                    {{ $album->title }}
                                </h3>
                            </a>
                            <div class="flex items-center justify-between mt-2 text-sm text-gray-500 dark:text-gray-400">
                                <span>📸 {{ $album->photo_count }} photos</span>
                                <span>👁️ {{ number_format($album->views) }}</span>
                            </div>

                            <!-- Status Badge for Photographer View -->
                            @auth
                                @if($showOnlyMine && $album->status !== 'published')
                                    <div class="mt-2">
                                        <span class="px-2 py-0.5 text-xs rounded-full {{ $this->getStatusBadge($album->status) }}">
                                            {{ ucfirst($album->status) }}
                                        </span>
                                    </div>
                                @endif
                            @endauth

                            <!-- Photographer Actions -->
                            @auth
                                @if($showOnlyMine && $album->photographer_id === auth()->id())
                                    <div class="mt-3 flex gap-2">
                                        <a href="{{ route('album.show', $album->slug) }}" class="flex-1 text-center px-2 py-1 text-sm bg-stage-600 text-white rounded hover:bg-stage-700 transition">
                                            View
                                        </a>
                                        @if($album->is_published)
                                            <button wire:click="unpublishAlbum({{ $album->id }})" class="px-2 py-1 text-sm text-yellow-600 border border-yellow-600 rounded hover:bg-yellow-50 transition">
                                                Unpublish
                                            </button>
                                        @else
                                            <button wire:click="publishAlbum({{ $album->id }})" class="px-2 py-1 text-sm text-green-600 border border-green-600 rounded hover:bg-green-50 transition">
                                                Publish
                                            </button>
                                        @endif
                                        <button wire:click="deleteAlbum({{ $album->id }})"
                                                onclick="return confirm('Move this album to trash?')"
                                                class="px-2 py-1 text-sm text-red-600 border border-red-600 rounded hover:bg-red-50 transition">
                                            Delete
                                        </button>
                                    </div>
                                @endif
                            @endauth
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="text-6xl mb-4">📸</div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No albums found</h3>
                        @auth
                            <p class="text-gray-500 dark:text-gray-400 mb-4">Create your first album by uploading photos</p>
                            <a href="{{ route('photo.upload') }}" class="px-4 py-2 bg-stage-600 text-white rounded-lg hover:bg-stage-700 transition">
                                Upload Photos
                            </a>
                        @else
                            <p class="text-gray-500 dark:text-gray-400">Check back later for new albums</p>
                        @endauth
                    </div>
                @endforelse
            </div>
        @else
            <!-- List View -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Album</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Photos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Views</th>
                        @auth
                            @if($showOnlyMine)
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            @endif
                        @endauth
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($this->albums as $album)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded bg-gray-200 dark:bg-gray-700 overflow-hidden">
                                        @if($album->cover_image_square)
                                            <img src="{{ $album->cover_image_square }}" alt="" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-lg">📷</div>
                                        @endif
                                    </div>
                                    <div>
                                        <a href="{{ route('album.show', $album->slug) }}" class="font-medium text-gray-900 dark:text-white hover:text-stage-600">
                                            {{ $album->title }}
                                        </a>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $album->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $album->photo_count }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ number_format($album->views) }}</td>
                            @auth
                                @if($showOnlyMine)
                                    <td class="px-6 py-4">
                                            <span class="px-2 py-1 text-xs rounded-full {{ $this->getStatusBadge($album->status) }}">
                                                {{ ucfirst($album->status) }}
                                            </span>
                                    </td>
                                @endif
                            @endauth
                            <td class="px-6 py-4 text-right">
                                <div class="flex gap-2 justify-end">
                                    <a href="{{ route('album.show', $album->slug) }}" class="text-stage-600 hover:text-stage-800 text-sm">View</a>
                                    @auth
                                        @if($showOnlyMine && $album->photographer_id === auth()->id())
                                            @if($album->is_published)
                                                <button wire:click="unpublishAlbum({{ $album->id }})" class="text-yellow-600 hover:text-yellow-800 text-sm">Unpublish</button>
                                            @else
                                                <button wire:click="publishAlbum({{ $album->id }})" class="text-green-600 hover:text-green-800 text-sm">Publish</button>
                                            @endif
                                            <button wire:click="deleteAlbum({{ $album->id }})"
                                                    onclick="return confirm('Move this album to trash?')"
                                                    class="text-red-600 hover:text-red-800 text-sm">
                                                Delete
                                            </button>
                                        @endif
                                    @endauth
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                No albums found
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Pagination -->
        <div class="mt-6">
            {{ $this->albums->links() }}
        </div>
    </div>

    @livewire('frontend.ui.footer', ['currentTeam' => $currentTeam])
</div>
