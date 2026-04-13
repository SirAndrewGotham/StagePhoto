@page
@layout('layout')

@php
    abort_unless(auth()->check(), 403);
    $teams = auth()->user()->teams;
    $personal = auth()->user()->personalTeam();
@endphp

<section class="px-4 sm:px-6 lg:px-8 py-6 max-w-md mx-auto">
    <h1 class="text-2xl font-bold mb-6">Выбрать сообщество</h1>

    <div class="space-y-3">
        @foreach($teams->merge([$personal]) as $t)
            <a href="{{ route('switch-team', ['team' => $t->id]) }}"
               class="block p-4 rounded-xl border {{ $team?->is($t) ? 'border-stage-500 bg-stage-50 dark:bg-stage-900/20' : 'border-gray-200 dark:border-gray-700' }} hover:shadow-md transition">
                <div class="font-semibold">{{ $t->name }}</div>
                <div class="text-sm text-gray-500">{{ $t->members_count ?? 'Личный аккаунт' }}</div>
            </a>
        @endforeach
    </div>
</section>
