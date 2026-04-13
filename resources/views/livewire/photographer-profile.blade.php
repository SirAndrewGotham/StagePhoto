<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;

class PhotographerProfile extends Component
{
    public User $photographer;
    public ?int $currentTeamId = null;

    public function mount(User $photographer, ?int $currentTeamId = null): void
    {
        abort_unless($photographer->hasRole('photographer'), 404);
        $this->photographer = $photographer->loadCount(['albums' => fn($q) => $q->published()]);
        $this->currentTeamId = $currentTeamId;
    }

    #[Computed]
    public function recentAlbums(): \Illuminate\Support\Collection
    {
        return $this->photographer->albums()
            ->published()
            ->with(['genres', 'photos' => fn($q) => $q->limit(1)->orderBy('sort_order')])
            ->latest('event_date')
            ->take(9)
            ->get();
    }

    public function render(): mixed
    {
        return view('livewire.photographer-profile');
    }
}
?>

<div x-data="{ activeTab: 'albums' }" class="w-full">
    {{-- Hero Section --}}
    <section class="relative px-4 sm:px-6 lg:px-8 py-12 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-950 border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-6xl mx-auto flex flex-col md:flex-row gap-6 items-start">
            <img :src="$wire.photographer.avatar_path || '/img/default-avatar.jpg'"
                 class="w-28 h-28 md:w-36 md:h-36 rounded-full object-cover border-4 border-white dark:border-gray-800 shadow-xl bg-gray-200 dark:bg-gray-700">
            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">{{ $photographer->name }}</h1>
                    @if($currentTeamId && $photographer->current_team_id == $currentTeamId)
                        <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-stage-100 dark:bg-stage-900/30 text-stage-700 dark:text-stage-300">🤝 Ваше сообщество</span>
                    @endif
                </div>
                <p class="text-gray-600 dark:text-gray-400 max-w-2xl">{{ $photographer->bio ?? 'Профессиональный концертный и театральный фотограф. Доступен для заказов по всей России.' }}</p>

                <div class="mt-4 flex flex-wrap gap-5 text-sm">
                    <div class="flex items-center gap-1">📸 <span class="font-semibold">{{ $photographer->albums_count }}</span> альбомов</div>
                    <div class="flex items-center gap-1">👁️ <span class="font-semibold">{{ number_format($photographer->albums->sum('views_count') ?? 0) }}</span> просмотров</div>
                    <div class="flex items-center gap-1 text-amber-500">⭐ <span class="font-semibold">4.9</span> средний рейтинг</div>
                </div>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('photographer.request', $photographer->username ?? $photographer->slug) }}"
                       class="px-5 py-2.5 bg-stage-600 hover:bg-stage-700 text-white font-medium rounded-xl transition shadow-md hover:shadow-lg">
                        📩 Заказать съёмку
                    </a>
                    <button class="px-5 py-2.5 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-xl transition">
                        🔗 Поделиться
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- Sticky Tabs --}}
    <div class="sticky top-16 z-40 bg-white/95 dark:bg-gray-900/95 backdrop-blur-sm border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex gap-6 overflow-x-auto scrollbar-thin">
            <button @click="activeTab = 'albums'" :class="activeTab === 'albums' ? 'text-stage-600 border-b-2 border-stage-600' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'" class="py-3 px-1 font-medium whitespace-nowrap transition">Альбомы</button>
            <button @click="activeTab = 'about'" :class="activeTab === 'about' ? 'text-stage-600 border-b-2 border-stage-600' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'" class="py-3 px-1 font-medium whitespace-nowrap transition">О фотографе</button>
            <button @click="activeTab = 'contact'" :class="activeTab === 'contact' ? 'text-stage-600 border-b-2 border-stage-600' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'" class="py-3 px-1 font-medium whitespace-nowrap transition">Контакты</button>
        </div>
    </div>

    {{-- Tab Content --}}
    <section class="px-4 sm:px-6 lg:px-8 py-8 max-w-6xl mx-auto min-h-[40vh]">
        {{-- Albums Tab --}}
        <div x-show="activeTab === 'albums'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="columns-1 sm:columns-2 lg:columns-3 gap-4 space-y-4">
                @foreach($this->recentAlbums as $album)
                    <article class="break-inside-avoid group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-md hover:shadow-xl transition-all duration-300">
                        <a href="{{ route('album.show', $album->slug) }}">
                            <img src="{{ $album->photos->first()?->thumbnail_path ?? asset('img/placeholder.webp') }}"
                                 loading="lazy"
                                 class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500">
                        </a>
                        <div class="p-4">
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white truncate">{{ $album->title }}</h3>
                            <div class="flex items-center justify-between mt-1 text-sm text-gray-500 dark:text-gray-400">
                                <span>{{ $album->event_date->format('d M Y') }}</span>
                                <span class="flex items-center gap-1 text-amber-500">⭐ {{ number_format($album->avg_rating, 1) }}</span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
            @if($this->recentAlbums->isEmpty())
                <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700">
                    <p class="text-gray-500 dark:text-gray-400">Альбомов пока нет 📷</p>
                </div>
            @endif
        </div>

        {{-- About Tab --}}
        <div x-show="activeTab === 'about'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <p class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $photographer->bio ?? 'Информация о фотографе скоро появится.' }}</p>
                @if($photographer->social_links)
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 flex gap-4">
                        @foreach(json_decode($photographer->social_links, true) as $platform => $url)
                            <a href="{{ $url }}" target="_blank" class="text-stage-600 hover:underline capitalize">{{ $platform }}</a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Contact Tab --}}
        <div x-show="activeTab === 'contact'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            <livewire:booking-request-form
                :photographer-id="$photographer->id"
                :current-team-id="$currentTeamId"
            />
        </div>
    </section>
</div>
