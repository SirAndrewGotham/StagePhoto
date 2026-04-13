@page
@layout('layout')

@php
    abort_unless(auth()->check(), 403, 'Требуется авторизация для отправки заявки');
    abort_unless($user->hasRole('photographer'), 404);
@endphp

<section class="px-4 sm:px-6 lg:px-8 py-6 max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Заказать фотографа: {{ $user->name }}</h1>

    <livewire:booking-request-form
        :photographer="$user"
        :currentTeam="$team"
        :albumId="request('album_id')"
    />
</section>
