<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use App\Models\Entity;

new class extends Component {
    use WithPagination;

    public Entity $entity;
    public $activeTab = 'albums';

    #[Title('{entity.name} - StagePhoto.ru')]
    public function mount(Entity $entity): void
    {
        $this->entity = $entity;
    }

    public function getProfileProperty()
    {
        return $this->entity->profile();
    }

    public function getContactsProperty()
    {
        return $this->entity->visibleContacts(auth()->user());
    }

    public function getHasPublicContactsProperty()
    {
        return $this->entity->contacts()->where('visibility', 'public')->exists();
    }

    public function getDedicatedAlbumsProperty()
    {
        return $this->entity->albums()
            ->where('is_published', true)
            ->with('photographer')
            ->latest()
            ->paginate(12);
    }

    public function getTaggedPhotosProperty()
    {
        return $this->entity->photos()
            ->where('status', 'published')
            ->with('album')
            ->latest()
            ->paginate(24);
    }

    public function getMembersProperty()
    {
        $entityType = $this->entity->entityable_type;
        if (in_array($entityType, [\App\Models\Band::class, \App\Models\Theater::class])) {
            return $this->entity->memberEntities()->with('entityable')->get();
        }
        return null;
    }

    public function getGroupsProperty()
    {
        if ($this->entity->entityable_type === \App\Models\Individual::class) {
            return $this->entity->parentEntities()->with('entityable')->get();
        }
        return null;
    }

    public function render()
    {
        return view('components.frontend.pages.persona.⚡show', [
            'profile' => $this->profile,
            'contacts' => $this->contacts,
            'hasPublicContacts' => $this->hasPublicContacts,
            'dedicatedAlbums' => $this->dedicatedAlbums,
            'taggedPhotos' => $this->taggedPhotos,
            'members' => $this->members,
            'groups' => $this->groups,
        ]);
    }
};

?>

<div>
    @livewire('frontend.ui.header', ['currentTeam' => null])

    <div class="pt-16">
        <!-- Hero Section with Cover Image -->
        <div class="relative">
            @if($profile->cover_path)
                <div class="h-64 md:h-96 w-full overflow-hidden">
                    <img src="{{ Storage::url($profile->cover_path) }}"
                         alt="{{ $profile->name }}"
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                </div>
            @endif

            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative -mt-8 md:-mt-12">
                <div class="flex flex-col md:flex-row items-center md:items-end gap-6">
                    <!-- Avatar -->
                    <div class="w-32 h-32 md:w-40 md:h-40 rounded-full border-4 border-white dark:border-gray-800 bg-white dark:bg-gray-800 overflow-hidden shadow-xl">
                        @if($profile->avatar_path)
                            <img src="{{ Storage::url($profile->avatar_path) }}"
                                 alt="{{ $profile->name }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-5xl bg-stage-100 dark:bg-stage-900/30">
                                {{ substr($profile->name, 0, 1) }}
                            </div>
                        @endif
                    </div>

                    <!-- Entity Info -->
                    <div class="flex-1 text-center md:text-left">
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white">
                            {{ $profile->name }}
                        </h1>
                        <div class="flex flex-wrap gap-2 mt-2 justify-center md:justify-start">
                            <!-- Type Tag -->
                            <a href="{{ route('albums.index', ['type' => $entity->type]) }}"
                               onclick="sessionStorage.setItem('returnToEntity', '{{ $entity->slug }}')"
                               class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-800 hover:bg-stage-100 dark:hover:bg-stage-900/30 hover:text-stage-600 dark:hover:text-stage-400 transition-colors">
                                {{ ucfirst($entity->type) }}
                            </a>

                            <!-- Genre Tag -->
                            @if($profile->genre)
                                <a href="{{ route('albums.index', ['genre' => Str::slug($profile->genre)]) }}"
                                   onclick="sessionStorage.setItem('returnToEntity', '{{ $entity->slug }}')"
                                   class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-800 hover:bg-stage-100 dark:hover:bg-stage-900/30 hover:text-stage-600 dark:hover:text-stage-400 transition-colors">
                                    {{ $profile->genre }}
                                </a>
                            @endif

                            <!-- Founded Year Tag -->
                            @if($profile->founded_year)
                                <a href="{{ route('albums.index', ['year' => $profile->founded_year]) }}"
                                   onclick="sessionStorage.setItem('returnToEntity', '{{ $entity->slug }}')"
                                   class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-800 hover:bg-stage-100 dark:hover:bg-stage-900/30 hover:text-stage-600 dark:hover:text-stage-400 transition-colors">
                                    🏛️ @lang('founded') {{ $profile->founded_year }}
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Contact Button -->
                    @auth
                        @if(count($contacts) > 0)
                            <div>
                                <button @click="$dispatch('open-contact-modal')"
                                        class="px-6 py-2 bg-stage-600 hover:bg-stage-700 text-white rounded-lg transition">
                                    📞 @lang('contact_info')
                                </button>
                            </div>
                        @endif
                    @else
                        @if($hasPublicContacts)
                            <div>
                                <a href="{{ route('login') }}"
                                   class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                                    🔒 @lang('login_to_see_contacts')
                                </a>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>

        <!-- Bio Section -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="prose prose-gray dark:prose-invert max-w-none">
                <p class="text-gray-600 dark:text-gray-400 text-lg leading-relaxed">
                    {{ $profile->bio }}
                </p>

                @if($profile->story)
                    <div class="mt-6">
                        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">@lang('story')</h2>
                        <div class="text-gray-600 dark:text-gray-400 leading-relaxed">
                            {{ $profile->story }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Members Section (for Bands/Theaters) -->
        @if($members && $members->count() > 0)
            <div class="border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/50 py-12">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6 text-center">
                        🎭 @lang('members')
                    </h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($members as $member)
                            <a href="{{ route('persona.show', $member) }}"
                               class="text-center group">
                                <div class="w-24 h-24 mx-auto rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 transition-transform group-hover:scale-105">
                                    @if($member->profile()->avatar_path)
                                        <img src="{{ Storage::url($member->profile()->avatar_path) }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-3xl">
                                            {{ substr($member->profile()->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <p class="mt-2 text-sm font-medium text-gray-900 dark:text-white group-hover:text-stage-600 transition-colors">
                                    {{ $member->profile()->name }}
                                </p>
                                @php
                                    $membership = $member->memberships->firstWhere('parent_entity_id', $entity->id);
                                @endphp
                                @if($membership && $membership->role)
                                    <p class="text-xs text-gray-500">{{ $membership->role }}</p>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Groups Section (for Individuals) -->
        @if($groups && $groups->count() > 0)
            <div class="border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/50 py-12">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6 text-center">
                        🎸 @lang('groups')
                    </h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
                        @foreach($groups as $group)
                            <a href="{{ route('persona.show', $group) }}"
                               class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition p-4 text-center">
                                @if($group->profile()->avatar_path)
                                    <img src="{{ Storage::url($group->profile()->avatar_path) }}"
                                         class="w-20 h-20 mx-auto rounded-full object-cover">
                                @else
                                    <div class="w-20 h-20 mx-auto rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-3xl">
                                        {{ substr($group->profile()->name, 0, 1) }}
                                    </div>
                                @endif
                                <h3 class="mt-3 font-semibold text-gray-900 dark:text-white">
                                    {{ $group->profile()->name }}
                                </h3>
                                <p class="text-xs text-gray-500">{{ $group->type }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Tabs -->
        <div class="border-b border-gray-200 dark:border-gray-800">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex gap-6">
                    <button wire:click="$set('activeTab', 'albums')"
                            class="py-3 px-1 border-b-2 transition-all {{ $activeTab === 'albums' ? 'border-stage-600 text-stage-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
                        📁 @lang('albums') ({{ $dedicatedAlbums->total() }})
                    </button>
                    <button wire:click="$set('activeTab', 'photos')"
                            class="py-3 px-1 border-b-2 transition-all {{ $activeTab === 'photos' ? 'border-stage-600 text-stage-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
                        📸 @lang('tagged_photos') ({{ $taggedPhotos->total() }})
                    </button>
                </div>
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if($activeTab === 'albums')
                @if($dedicatedAlbums->count() > 0)
                    <div class="masonry-grid">
                        @foreach($dedicatedAlbums as $album)
                            @livewire('frontend.islands.album-card', ['album' => $album], key('album-' . $album->id))
                        @endforeach
                    </div>
                    <div class="mt-8">
                        {{ $dedicatedAlbums->links() }}
                    </div>
                @else
                    <p class="text-center text-gray-500 py-12">@lang('no_dedicated_albums')</p>
                @endif
            @else
                @if($taggedPhotos->count() > 0)
                    <div class="masonry-grid">
                        @foreach($taggedPhotos as $photo)
                            @livewire('frontend.islands.photo-card', ['photo' => $photo], key('photo-' . $photo->id))
                        @endforeach
                    </div>
                    <div class="mt-8">
                        {{ $taggedPhotos->links() }}
                    </div>
                @else
                    <p class="text-center text-gray-500 py-12">@lang('no_tagged_photos')</p>
                @endif
            @endif
        </div>

        <!-- Contact Modal -->
        @auth
            @if(count($contacts) > 0)
                <div x-data="{ open: false }"
                     @open-contact-modal.window="open = true"
                     x-show="open"
                     x-cloak>
                    <div class="fixed inset-0 z-50 overflow-y-auto">
                        <div class="fixed inset-0 bg-black/50" @click="open = false"></div>
                        <div class="relative min-h-screen flex items-center justify-center p-4">
                            <div class="relative max-w-md w-full bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">📞 @lang('contact_info')</h3>
                                    <button @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">✕</button>
                                </div>
                                <div class="space-y-3">
                                    @foreach($contacts as $contact)
                                        <div class="flex items-center gap-3">
                                            <span class="text-lg">
                                                @switch($contact->contact_type)
                                                    @case('email') 📧 @break
                                                    @case('phone') 📱 @break
                                                    @case('telegram') 💬 @break
                                                    @case('vkontakte') 🌐 @break
                                                    @case('instagram') 📷 @break
                                                    @default 🔗
                                                @endswitch
                                            </span>
                                            <div>
                                                <p class="text-xs text-gray-500 uppercase">{{ $contact->contact_type }}</p>
                                                @if($contact->contact_type === 'email')
                                                    <a href="mailto:{{ $contact->value }}" class="text-gray-900 dark:text-white hover:text-stage-600">
                                                        {{ $contact->value }}
                                                    </a>
                                                @elseif($contact->contact_type === 'phone')
                                                    <a href="tel:{{ $contact->value }}" class="text-gray-900 dark:text-white">
                                                        {{ $contact->value }}
                                                    </a>
                                                @elseif($contact->contact_type === 'telegram')
                                                    <a href="https://t.me/{{ ltrim($contact->value, '@') }}" target="_blank" class="text-gray-900 dark:text-white hover:text-stage-600">
                                                        {{ $contact->value }}
                                                    </a>
                                                @elseif($contact->contact_type === 'vkontakte')
                                                    <a href="{{ Str::startsWith($contact->value, 'http') ? $contact->value : 'https://' . $contact->value }}" target="_blank" class="text-gray-900 dark:text-white hover:text-stage-600">
                                                        {{ $contact->value }}
                                                    </a>
                                                @else
                                                    <p class="text-gray-900 dark:text-white">{{ $contact->value }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button @click="open = false"
                                        class="mt-6 w-full px-4 py-2 bg-stage-600 text-white rounded-lg hover:bg-stage-700 transition">
                                    @lang('close')
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endauth
    </div>

    @livewire('frontend.ui.footer', ['currentTeam' => null])
</div>
