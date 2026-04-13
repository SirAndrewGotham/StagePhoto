<?php

namespace App\Livewire;

use App\Models\BookingRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;
use Livewire\Component;

class BookingRequestForm extends Component
{
    public int $photographerId;
    public ?int $currentTeamId = null;
    public ?int $albumId = null;

    #[Rule('required|string|min:10|max:2000')]
    public string $message = '';
    #[Rule('nullable|date|after_or_equal:today')]
    public ?string $dateStart = null;
    #[Rule('nullable|date|after:dateStart')]
    public ?string $dateEnd = null;
    #[Rule('nullable|string|max:500')]
    public ?string $budgetNotes = null;

    public bool $success = false;
    public string $successMessage = '';

    public function mount(int $photographerId, ?int $currentTeamId = null, ?int $albumId = null): void
    {
        $this->photographerId = $photographerId;
        $this->currentTeamId = $currentTeamId;
        $this->albumId = $albumId;
        abort_unless(User::where('id', $photographerId)->whereHas('roles', fn($q) => $q->where('slug', 'photographer'))->exists(), 404);
    }

    public function submit(): void
    {
        if (!Auth::check()) {
            $this->dispatch('auth-required');
            return;
        }

        $this->validate();

        BookingRequest::create([
            'requester_id'       => Auth::id(),
            'photographer_id'    => $this->photographerId,
            'album_id'           => $this->albumId,
            'team_id'            => $this->currentTeamId,
            'message'            => $this->message,
            'desired_date_start' => $this->dateStart,
            'desired_date_end'   => $this->dateEnd,
            'budget_notes'       => $this->budgetNotes,
            'status'             => 'pending',
        ]);

        $this->success = true;
        $this->successMessage = '✅ Заявка успешно отправлена! Фотограф получит уведомление.';
        $this->reset(['message', 'dateStart', 'dateEnd', 'budgetNotes']);
    }

    public function render()
    {
        return view('livewire.booking-request-form');
    }
}
?>

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
    @if($success)
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-green-700 dark:text-green-300">
            {{ $successMessage }}
        </div>
        <button wire:click="$set('success', false)" class="text-sm text-green-600 dark:text-green-400 hover:underline">Отправить ещё</button>
    @else
        <form wire:submit="submit" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Сообщение *</label>
                <textarea wire:model="message" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-stage-500 focus:border-transparent resize-none"
                          placeholder="Опишите мероприятие, стиль, ожидания..."></textarea>
                @error('message') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Дата начала</label>
                    <input type="date" wire:model="dateStart"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-stage-500">
                    @error('dateStart') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Дата окончания</label>
                    <input type="date" wire:model="dateEnd"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-stage-500">
                    @error('dateEnd') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Бюджет / Примечания</label>
                <input type="text" wire:model="budgetNotes" placeholder="Например: 30 000 ₽, требуется доставка в область..."
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-stage-500">
                @error('budgetNotes') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <button type="submit" wire:loading.attr="disabled" wire:target="submit"
                    class="w-full sm:w-auto px-6 py-2.5 bg-stage-600 hover:bg-stage-700 text-white font-medium rounded-xl transition-colors shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-wait flex items-center justify-center gap-2">
                <span wire:loading.remove wire:target="submit">📤 Отправить заявку</span>
                <span wire:loading wire:target="submit">⏳ Отправка...</span>
            </button>
        </form>
    @endif
</div>

{{-- Auth redirect listener --}}
<script>
    document.addEventListener('livewire:initialized', () => {
        @this.on('auth-required', () => {
            window.location.href = '{{ route('login') }}?redirect=' + encodeURIComponent(window.location.href);
        });
    });
</script>
```

---

## 🛠️ Integration Notes

### 1. Usage in Folio Pages
```blade
{{-- resources/views/pages/index.blade.php --}}
@page
@layout('layout')

@php
    $teamId = auth()->user()?->current_team_id;
@endphp

<livewire:albums-grid :current-team-id="$teamId" />
```

```blade
{{-- resources/views/pages/photographer/[user:username]/request.blade.php --}}
@page
@layout('layout')

@php
    abort_unless(auth()->check(), 403, 'Требуется авторизация');
    $teamId = auth()->user()?->current_team_id;
@endphp

<section class="px-4 sm:px-6 lg:px-8 py-6 max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Заказать фотографа: {{ $user->name }}</h1>
    <livewire:booking-request-form
        :photographer-id="$user->id"
        :current-team-id="$teamId"
        :album-id="request('album_id')"
    />
</section>
