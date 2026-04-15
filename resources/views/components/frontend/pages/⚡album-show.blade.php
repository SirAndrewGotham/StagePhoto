<?php

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use App\Models\Album;
use App\Models\Tag;

new class extends Component {
    public Album $album;

    #[Url(history: true)]
    public $photoPage = 1;

    #[Url(history: true)]
    public $commentPage = 1;

    public $selectedPhoto;
    public $commentContent = '';
    public $photoCommentContent = '';
    public $replyTo;
    public $photoReplyTo;
    public $perPage = 20;
    public $showModal = false;

    public function mount(Album $album): void
    {
        $this->album = $album;
        $this->album->increment('views');
    }

    #[Computed]
    public function photos()
    {
        return $this->album
            ->photos()
            ->orderBy('sort_order')
            ->paginate($this->perPage, ['*'], 'photoPage', $this->photoPage);
    }

    #[Computed]
    public function comments()
    {
        return $this->album
            ->approvedComments()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->recent()
            ->paginate(20, ['*'], 'commentPage', $this->commentPage);
    }

    #[Computed]
    public function commentCount()
    {
        return $this->album->approvedComments()->count();
    }

    #[Computed]
    public function relatedAlbums()
    {
        $categoryIds = $this->album->categories->pluck('id');

        if ($categoryIds->isEmpty()) {
            return collect();
        }

        return Album::whereHas('categories', function($query) use ($categoryIds) {
            $query->whereIn('categories.id', $categoryIds);
        })
            ->where('id', '!=', $this->album->id)
            ->where('is_published', true)
            ->limit(4)
            ->get();
    }

    #[Computed]
    public function currentPhoto()
    {
        if ($this->selectedPhoto) {
            return $this->album->photos()->find($this->selectedPhoto);
        }
        return null;
    }

    #[Computed]
    public function photoComments()
    {
        if ($this->currentPhoto) {
            return $this->currentPhoto
                ->approvedComments()
                ->whereNull('parent_id')
                ->with(['user', 'replies.user'])
                ->recent()
                ->get();
        }
        return collect();
    }

    #[Computed]
    public function photoCommentCount()
    {
        return $this->currentPhoto ? $this->currentPhoto->approvedComments()->count() : 0;
    }

    #[Computed]
    public function photos()
    {
        return $this->album
            ->photos()  // This automatically excludes soft deleted photos
            ->orderBy('sort_order')
            ->paginate($this->perPage, ['*'], 'photoPage', $this->photoPage);
    }

    public function selectPhoto($photoId): void
    {
        $this->selectedPhoto = $photoId;
        $this->showModal = true;
        $this->photoCommentContent = '';
        $this->photoReplyTo = null;

        $photo = $this->album->photos()->find($photoId);
        if ($photo) {
            $photo->increment('views');
        }
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->selectedPhoto = null;
        $this->photoCommentContent = '';
        $this->photoReplyTo = null;
    }

    public function nextPhoto(): void
    {
        $currentIndex = $this->photos->search(fn($photo) => $photo->id == $this->selectedPhoto);
        $nextPhoto = $this->photos->get($currentIndex + 1);

        if ($nextPhoto) {
            $this->selectedPhoto = $nextPhoto->id;
            $nextPhoto->increment('views');
            $this->photoCommentContent = '';
            $this->photoReplyTo = null;
        }
    }

    public function previousPhoto(): void
    {
        $currentIndex = $this->photos->search(fn($photo) => $photo->id == $this->selectedPhoto);
        $prevPhoto = $this->photos->get($currentIndex - 1);

        if ($prevPhoto) {
            $this->selectedPhoto = $prevPhoto->id;
            $prevPhoto->increment('views');
            $this->photoCommentContent = '';
            $this->photoReplyTo = null;
        }
    }

    public function addComment(): void
    {
        $this->validate([
            'commentContent' => 'required|string|min:2|max:1000',
        ]);

        $comment = $this->album->comments()->create([
            'user_id' => auth()->id(),
            'content' => $this->commentContent,
            'parent_id' => $this->replyTo,
            'is_approved' => true,
        ]);

        $this->commentContent = '';
        $this->replyTo = null;
        $this->dispatch('comment-added', commentId: $comment->id);
    }

    public function addPhotoComment(): void
    {
        $this->validate([
            'photoCommentContent' => 'required|string|min:2|max:1000',
        ]);

        if ($this->currentPhoto) {
            $comment = $this->currentPhoto->comments()->create([
                'user_id' => auth()->id(),
                'content' => $this->photoCommentContent,
                'parent_id' => $this->photoReplyTo,
                'is_approved' => true,
            ]);

            $this->photoCommentContent = '';
            $this->photoReplyTo = null;
            $this->dispatch('photo-comment-added', commentId: $comment->id);
        }
    }

    public function addTagToAlbum($tagName): void
    {
        $tag = Tag::firstOrCreate(
            ['slug' => \Illuminate\Support\Str::slug($tagName)],
            ['name' => $tagName]
        );

        if (!$this->album->tags->contains($tag->id)) {
            $this->album->tags()->attach($tag->id);
            $tag->incrementUsage();
        }

        $this->dispatch('tag-added', tag: $tag->name);
    }

    public function removeTagFromAlbum($tagId): void
    {
        $tag = Tag::find($tagId);
        if ($tag && $this->album->tags()->detach($tagId)) {
            $tag->decrementUsage();
        }

        $this->dispatch('tag-removed');
    }

    public function likeComment($commentId): void
    {
        $comment = \App\Models\Comment::find($commentId);
        if ($comment) {
            $comment->increment('likes');
        }
    }

    public function likePhotoComment($commentId): void
    {
        $comment = \App\Models\Comment::find($commentId);
        if ($comment) {
            $comment->increment('likes');
        }
    }

    public function loadMorePhotos(): void
    {
        $this->photoPage++;
    }

    public function rateAlbum($rating): void
    {
        if (!auth()->check()) {
            $this->dispatch('show-login-modal');
            return;
        }

        $this->album->rate(auth()->id(), $rating);
        $this->dispatch('album-rated', rating: $rating);
        // Refresh computed properties
        unset($this->album);
    }

    public function toggleCommentLike($commentId): void
    {
        if (!auth()->check()) {
            $this->dispatch('show-login-modal');
            return;
        }

        $comment = \App\Models\Comment::find($commentId);
        if ($comment) {
            $comment->toggleLike(auth()->id());
            $this->dispatch('comment-likes-updated');
        }
    }

    public function openRequestModal(): void
    {
        $this->dispatch('open-request-modal', albumId: $this->album->id);
    }
};

?>

<div>
    @livewire('frontend.ui.header')
    @livewire('frontend.islands.filter-bar')

    <div class="min-h-screen bg-gray-50 dark:bg-gray-950">
        <!-- Album Header -->
        <div class="relative h-96 overflow-hidden">
            <img src="{{ $album->cover_image }}" alt="{{ $album->title }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                <div class="max-w-7xl mx-auto">
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-2">{{ $album->title }}</h1>
                    <div class="flex flex-wrap gap-4 text-sm">
                        <span>📷 {{ $album->photographer->name ?? 'Unknown' }}</span>
                        <span>📅 {{ $album->event_date->format('F j, Y') }}</span>
                        <span>📍 {{ $album->venue }}</span>
                        <span>📸 {{ $album->photos()->count() }} photos</span>
                        <span>👁️ {{ number_format($album->views) }} views</span>
                        <!-- Rating Section -->
                        <div class="flex items-center gap-2" x-data="{ rating: {{ $album->user_rating ?? 0 }}, hoverRating: 0 }">
                            <div class="flex items-center gap-1">
                                <template x-for="star in [1,2,3,4,5]">
                                    <button
                                        @click="$wire.rateAlbum(star)"
                                        @mouseenter="hoverRating = star"
                                        @mouseleave="hoverRating = 0"
                                        class="text-xl transition-colors focus:outline-none"
                                        :class="{
                                            'text-yellow-400': (hoverRating ? star <= hoverRating : star <= (rating || {{ $album->average_rating }})),
                                        'text-gray-300 dark:text-gray-600': !(hoverRating ? star <= hoverRating : star <= (rating || {{ $album->average_rating }}))
                                        }"
                                    >
                                        ★
                                    </button>
                                </template>
                            </div>
                            <span class="text-sm">
                                {{ number_format($album->average_rating, 1) }} ({{ $album->rating_count }} {{ __('ratings') }})
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    @if($album->description)
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 mb-6 shadow-sm">
                            <p class="text-gray-700 dark:text-gray-300">{{ $album->description }}</p>
                        </div>
                    @endif

                    @if($album->categories->isNotEmpty())
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 mb-6 shadow-sm">
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Categories</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($album->categories as $category)
                                    <span class="px-3 py-1 text-sm rounded-full bg-stage-100 text-stage-800 dark:bg-stage-900 dark:text-stage-200">{{ $category->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Photo Grid -->
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Photos</h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($this->photos as $photo)
                                <div wire:click="selectPhoto({{ $photo->id }})" class="group relative aspect-square overflow-hidden rounded-xl cursor-pointer bg-gray-100 dark:bg-gray-800" role="button" tabindex="0">
                                    <img src="{{ $photo->thumbnail_path ?? $photo->path }}" alt="{{ $album->title }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/50 transition-colors duration-300 flex items-center justify-center">
                                        <span class="text-white text-sm font-medium opacity-0 group-hover:opacity-100 transition-opacity duration-300">🔍 View</span>
                                    </div>
                                    @if($photo->is_featured)
                                        <span class="absolute top-2 right-2 px-2 py-0.5 text-xs bg-yellow-500 text-white rounded-full">★</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        @if($this->photos->hasMorePages())
                            <div class="text-center mt-6">
                                <button wire:click="loadMorePhotos" wire:loading.attr="disabled" class="px-6 py-2 bg-stage-600 text-white rounded-lg hover:bg-stage-700 transition">
                                    <span wire:loading.remove>Load More Photos</span>
                                    <span wire:loading>Loading...</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Request Button Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 mb-6 shadow-sm text-center">
                        <div class="mb-3">
                            <span class="text-4xl">📸</span>
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">{{ __('album.request_photographer') }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            {{ __('album.request_description') }}
                        </p>
                        <button
                            wire:click="openRequestModal"
                            class="w-full px-4 py-2 bg-stage-600 text-white rounded-lg hover:bg-stage-700 transition-colors"
                        >
                            {{ __('album.request') }}
                        </button>
                    </div>

                    <!-- Related Albums -->
                    @if($this->relatedAlbums->isNotEmpty())
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 mb-6 shadow-sm">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-4">{{ __('album.related_albums') }}</h3>
                            <div class="space-y-3">
                                @foreach($this->relatedAlbums as $related)
                                    <a href="{{ route('album.show', $related->slug) }}" wire:navigate class="block group">
                                        <div class="flex gap-3">
                                            <img src="{{ $related->cover_image }}" alt="{{ $related->title }}" class="w-16 h-16 object-cover rounded-lg">
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-stage-600 transition">
                                                    {{ $related->title }}
                                                </h4>
                                                <p class="text-xs text-gray-500">{{ $related->photos()->count() }} {{ __('album.photos') }}</p>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Album Comments Section -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Comments ({{ $this->commentCount }})</h3>

                        @auth
                            <div class="mb-6">
                                @if($replyTo)
                                    <div class="mb-2 text-sm text-stage-600">
                                        Replying to comment
                                        <button wire:click="$set('replyTo', null)" class="ml-2 text-gray-500 hover:text-gray-700">Cancel</button>
                                    </div>
                                @endif
                                <textarea wire:model="commentContent" placeholder="Add a comment..." rows="3" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-stage-500 focus:border-transparent dark:bg-gray-800"></textarea>
                                @error('commentContent') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                <button wire:click="addComment" class="mt-2 px-4 py-2 text-sm bg-stage-600 text-white rounded-lg hover:bg-stage-700 transition">Post Comment</button>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4"><a href="/login" class="text-stage-600 hover:underline">Login</a> to comment</p>
                        @endauth

                        <div class="space-y-4 max-h-96 overflow-y-auto">
                            @forelse($this->comments as $comment)
                                <div class="border-b dark:border-gray-700 pb-3">
                                    <div class="flex justify-between items-start mb-1">
                                        <div>
                                            <span class="font-semibold text-sm text-gray-900 dark:text-white">{{ $comment->user->name ?? 'Anonymous' }}</span>
                                            <span class="text-xs text-gray-500 ml-2">{{ $comment->time_ago }}</span>
                                        </div>
                                        <button
                                            wire:click="toggleCommentLike({{ $comment->id }})"
                                            class="text-xs transition-colors {{ $comment->is_liked_by_user ? 'text-red-500' : 'text-gray-500 hover:text-red-500' }}"
                                        >
                                            ❤️ {{ $comment->likes }}
                                        </button>
                                    </div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $comment->content }}</p>
                                    @auth
                                        <button wire:click="$set('replyTo', {{ $comment->id }})" class="mt-1 text-xs text-stage-600 hover:underline">Reply</button>
                                    @endauth
                                    @foreach($comment->replies as $reply)
                                        <div class="ml-4 mt-2 pl-3 border-l-2 border-gray-200 dark:border-gray-600">
                                            <div class="flex justify-between items-start mb-1">
                                                <div><span class="font-semibold text-xs text-gray-900 dark:text-white">{{ $reply->user->name ?? 'Anonymous' }}</span><span class="text-xs text-gray-500 ml-2">{{ $reply->time_ago }}</span></div>
                                                <button wire:click="likeComment({{ $reply->id }})" class="text-xs text-gray-500 hover:text-red-500">❤️ {{ $reply->likes }}</button>
                                            </div>
                                            <p class="text-xs text-gray-700 dark:text-gray-300">{{ $reply->content }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 text-center py-4">No comments yet. Be the first!</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Photo Modal with Comments -->
    @if($showModal && $this->currentPhoto)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-black/90" wire:click="closeModal"></div>

            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div class="relative max-w-6xl w-full bg-white dark:bg-gray-800 rounded-2xl overflow-hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-2">
                        <!-- Photo -->
                        <div class="relative bg-black">
                            <img src="{{ $this->currentPhoto->path }}" alt="{{ $album->title }}" class="w-full h-auto">

                            @if($this->photos->count() > 1)
                                <button wire:click="previousPhoto" class="absolute left-2 top-1/2 -translate-y-1/2 p-2 bg-black/50 text-white rounded-full hover:bg-black/75 transition">◀</button>
                                <button wire:click="nextPhoto" class="absolute right-2 top-1/2 -translate-y-1/2 p-2 bg-black/50 text-white rounded-full hover:bg-black/75 transition">▶</button>
                            @endif

                            <button wire:click="closeModal" class="absolute top-2 right-2 p-2 bg-black/50 text-white rounded-full hover:bg-black/75 transition">✕</button>
                        </div>

                        <!-- Photo Comments Section -->
                        <div class="flex flex-col h-full max-h-[80vh] overflow-hidden">
                            <!-- Photo Info with Request Button -->
                            <div class="p-4 border-b dark:border-gray-700">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Photo {{ $this->photos->search(fn($p) => $p->id == $selectedPhoto) + 1 }} of {{ $this->photos->count() }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-1">{{ $this->currentPhoto->views }} views</p>
                                    </div>
                                    <div class="flex gap-2">
                                        @if($this->currentPhoto->is_featured)
                                            <span class="px-2 py-1 text-xs bg-yellow-500 text-white rounded-full">Featured</span>
                                        @endif
                                        <!-- Request Button for Photo -->
                                        <button
                                            wire:click="openRequestModal"
                                            class="px-3 py-1 text-xs bg-stage-600 text-white rounded-lg hover:bg-stage-700 transition-colors flex items-center gap-1"
                                        >
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                            </svg>
                                            {{ __('album.request_this_photo') }}
                                        </button>
                                    </div>
                                </div>
                                @if($this->currentPhoto->description)
                                    <p class="mt-3 text-sm text-gray-700 dark:text-gray-300">{{ $this->currentPhoto->description }}</p>
                                @endif
                            </div>

                            <!-- Photo Comments List -->
                            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                                <h4 class="font-semibold text-gray-900 dark:text-white">Comments ({{ $this->photoCommentCount }})</h4>

                                @auth
                                    <div class="mb-4">
                                        @if($photoReplyTo)
                                            <div class="mb-2 text-sm text-stage-600">
                                                Replying to comment
                                                <button wire:click="$set('photoReplyTo', null)" class="ml-2 text-gray-500 hover:text-gray-700">Cancel</button>
                                            </div>
                                        @endif
                                        <textarea wire:model="photoCommentContent" placeholder="Add a comment to this photo..." rows="2" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-stage-500 focus:border-transparent dark:bg-gray-800"></textarea>
                                        @error('photoCommentContent') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        <button wire:click="addPhotoComment" class="mt-2 px-3 py-1 text-sm bg-stage-600 text-white rounded-lg hover:bg-stage-700 transition">Post Comment</button>
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4"><a href="/login" class="text-stage-600 hover:underline">Login</a> to comment on this photo</p>
                                @endauth

                                @forelse($this->photoComments as $comment)
                                    <div class="border-b dark:border-gray-700 pb-3">
                                        <div class="flex justify-between items-start mb-1">
                                            <div>
                                                <span class="font-semibold text-sm text-gray-900 dark:text-white">{{ $comment->user->name ?? 'Anonymous' }}</span>
                                                <span class="text-xs text-gray-500 ml-2">{{ $comment->time_ago }}</span>
                                            </div>
                                            <button wire:click="likePhotoComment({{ $comment->id }})" class="text-xs text-gray-500 hover:text-red-500">❤️ {{ $comment->likes }}</button>
                                        </div>
                                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $comment->content }}</p>
                                        @auth
                                            <button wire:click="$set('photoReplyTo', {{ $comment->id }})" class="mt-1 text-xs text-stage-600 hover:underline">Reply</button>
                                        @endauth
                                        @foreach($comment->replies as $reply)
                                            <div class="ml-4 mt-2 pl-3 border-l-2 border-gray-200 dark:border-gray-600">
                                                <div class="flex justify-between items-start mb-1">
                                                    <div>
                                                        <span class="font-semibold text-xs text-gray-900 dark:text-white">{{ $reply->user->name ?? 'Anonymous' }}</span>
                                                        <span class="text-xs text-gray-500 ml-2">{{ $reply->time_ago }}</span>
                                                    </div>
                                                    <button wire:click="likePhotoComment({{ $reply->id }})" class="text-xs text-gray-500 hover:text-red-500">❤️ {{ $reply->likes }}</button>
                                                </div>
                                                <p class="text-xs text-gray-700 dark:text-gray-300">{{ $reply->content }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500 text-center py-4">No comments yet on this photo. Be the first!</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Request Modal -->
    @livewire('frontend.ui.request-modal')

    @livewire('frontend.ui.footer')
</div>
