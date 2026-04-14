<?php

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use App\Models\Photo;
use App\Models\Tag;

new class extends Component {
    public Photo $photo;
    public $commentContent = '';
    public $replyTo;

    #[Url(history: true)]
    public $page = 1;

    public function mount(Photo $photo): void
    {
        $this->photo = $photo;
        $this->photo->incrementViews();
    }

    #[Computed]
    public function comments()
    {
        return $this->photo
            ->approvedComments()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->recent()
            ->paginate(20, ['*'], 'page', $this->page);
    }

    #[Computed]
    public function relatedPhotos()
    {
        if ($this->photo->tags->isEmpty()) {
            return collect();
        }

        $tagIds = $this->photo->tags->pluck('id');

        return Photo::whereHas('tags', function($query) use ($tagIds) {
            $query->whereIn('tags.id', $tagIds);
        })
            ->where('id', '!=', $this->photo->id)
            ->limit(6)
            ->get();
    }

    public function addComment(): void
    {
        $this->validate([
            'commentContent' => 'required|string|min:2|max:1000',
        ]);

        $comment = $this->photo->comments()->create([
            'user_id' => auth()->id() ?? 1, // Fallback to guest handling
            'content' => $this->commentContent,
            'parent_id' => $this->replyTo,
            'is_approved' => auth()->check(), // Auto-approve for logged-in users
        ]);

        $this->commentContent = '';
        $this->replyTo = null;

        $this->dispatch('comment-added', commentId: $comment->id);
        $this->dispatch('$refresh');
    }

    public function addTag($tagName): void
    {
        $tag = Tag::firstOrCreate(
            ['slug' => \Illuminate\Support\Str::slug($tagName)],
            ['name' => $tagName]
        );

        if (!$this->photo->tags->contains($tag->id)) {
            $this->photo->tags()->attach($tag->id);
            $tag->incrementUsage();
        }

        $this->dispatch('tag-added', tag: $tag->name);
    }

    public function removeTag($tagId): void
    {
        $tag = Tag::find($tagId);
        if ($tag && $this->photo->tags()->detach($tagId)) {
            $tag->decrementUsage();
        }

        $this->dispatch('tag-removed');
    }

    public function likeComment($commentId): void
    {
        $comment = Comment::find($commentId);
        if ($comment) {
            $comment->increment('likes');
        }
    }

    public function render(): string
    {
        return <<<'HTML'
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Photo Viewer -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                    <div class="relative">
                        <img
                            src="{{ $photo->path }}"
                            alt="{{ $photo->album->title }} - Photo {{ $loop->index + 1 }}"
                            class="w-full h-auto"
                        >
                    </div>

                    <div class="p-6">
                        <!-- Photo Info -->
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $photo->album->title }}
                                </h1>
                                <p class="text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $photo->created_at->format('F j, Y') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">📷 {{ $photo->album->photographer->name ?? 'Unknown' }}</p>
                                <p class="text-sm text-gray-500">👁️ {{ number_format($photo->views) }} views</p>
                            </div>
                        </div>

                        @if($photo->description)
                            <p class="text-gray-700 dark:text-gray-300 mb-4">{{ $photo->description }}</p>
                        @endif

                        <!-- Tags Section -->
                        <div class="mb-6">
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tags</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($photo->tags as $tag)
                                    <span class="px-2 py-1 text-xs rounded-full {{ $tag->color_class }}">
                                        #{{ $tag->name }}
                                        @auth
                                            <button wire:click="removeTag({{ $tag->id }})" class="ml-1 hover:text-red-600">×</button>
                                        @endauth
                                    </span>
                                @endforeach

                                @auth
                                    <div x-data="{ showInput: false, newTag: '' }" class="relative">
                                        <button
                                            @click="showInput = !showInput"
                                            class="px-2 py-1 text-xs rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300"
                                        >
                                            + Add Tag
                                        </button>
                                        <div x-show="showInput" @click.away="showInput = false" x-cloak class="absolute top-full left-0 mt-1 z-10">
                                            <div class="flex gap-1 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-1">
                                                <input
                                                    type="text"
                                                    x-model="newTag"
                                                    placeholder="Tag name..."
                                                    class="px-2 py-1 text-sm border rounded dark:bg-gray-700"
                                                    @keyup.enter="if(newTag) $wire.addTag(newTag); newTag = ''; showInput = false"
                                                >
                                                <button
                                                    @click="if(newTag) $wire.addTag(newTag); newTag = ''; showInput = false"
                                                    class="px-2 py-1 text-xs bg-stage-600 text-white rounded"
                                                >
                                                    Add
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endauth
                            </div>
                        </div>

                        <!-- Related Photos -->
                        @if($this->relatedPhotos->isNotEmpty())
                            <div class="mb-6">
                                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Related Photos</h3>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-2">
                                    @foreach($this->relatedPhotos as $related)
                                        <a href="/photo/{{ $related->id }}">
                                            <img src="{{ $related->thumbnail_path ?? $related->path }}"
                                                 alt="Related photo"
                                                 class="w-full h-20 object-cover rounded-lg hover:opacity-75 transition">
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Comments Section -->
                        <div class="border-t dark:border-gray-700 pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                Comments ({{ $photo->approvedComments()->count() }})
                            </h3>

                            <!-- Comment Form -->
                            @auth
                                <div class="mb-6">
                                    @if($replyTo)
                                        <div class="mb-2 text-sm text-stage-600">
                                            Replying to comment
                                            <button wire:click="$set('replyTo', null)" class="ml-2 text-gray-500 hover:text-gray-700">Cancel</button>
                                        </div>
                                    @endif
                                    <textarea
                                        wire:model="commentContent"
                                        placeholder="Add a comment..."
                                        rows="3"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-stage-500 focus:border-transparent dark:bg-gray-800"
                                    ></textarea>
                                    @error('commentContent') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    <button
                                        wire:click="addComment"
                                        class="mt-2 px-4 py-2 bg-stage-600 text-white rounded-lg hover:bg-stage-700 transition"
                                    >
                                        Post Comment
                                    </button>
                                </div>
                            @else
                                <p class="text-gray-500 dark:text-gray-400 mb-4">
                                    <a href="/login" class="text-stage-600 hover:underline">Login</a> to comment
                                </p>
                            @endauth

                            <!-- Comments List -->
                            <div class="space-y-4">
                                @foreach($this->comments as $comment)
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <span class="font-semibold text-gray-900 dark:text-white">
                                                    {{ $comment->user->name ?? 'Anonymous' }}
                                                </span>
                                                <span class="text-xs text-gray-500 ml-2">{{ $comment->time_ago }}</span>
                                            </div>
                                            <button
                                                wire:click="likeComment({{ $comment->id }})"
                                                class="text-sm text-gray-500 hover:text-red-500"
                                            >
                                                ❤️ {{ $comment->likes }}
                                            </button>
                                        </div>
                                        <p class="text-gray-700 dark:text-gray-300">{{ $comment->content }}</p>

                                        @auth
                                            <button
                                                wire:click="$set('replyTo', {{ $comment->id }})"
                                                class="mt-2 text-xs text-stage-600 hover:underline"
                                            >
                                                Reply
                                            </button>
                                        @endauth

                                        <!-- Replies -->
                                        @foreach($comment->replies as $reply)
                                            <div class="ml-6 mt-3 pl-4 border-l-2 border-gray-200 dark:border-gray-600">
                                                <div class="flex justify-between items-start mb-1">
                                                    <div>
                                                        <span class="font-semibold text-sm text-gray-900 dark:text-white">
                                                            {{ $reply->user->name ?? 'Anonymous' }}
                                                        </span>
                                                        <span class="text-xs text-gray-500 ml-2">{{ $reply->time_ago }}</span>
                                                    </div>
                                                    <button
                                                        wire:click="likeComment({{ $reply->id }})"
                                                        class="text-xs text-gray-500 hover:text-red-500"
                                                    >
                                                        ❤️ {{ $reply->likes }}
                                                    </button>
                                                </div>
                                                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $reply->content }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4">
                                {{ $this->comments->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        HTML;
    }
};
?>
