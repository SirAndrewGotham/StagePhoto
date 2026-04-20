<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use App\Models\Photo;
use Illuminate\Support\Facades\Password;

new class extends Component {
    public $email = '';
    public $featuredPhoto;
    public $statusMessage = '';
    public $errorMessage = '';

    #[Title('Reset Password - StagePhoto.ru')]
    public function mount(): void
    {
        $this->featuredPhoto = Photo::with('album.photographer')
            ->where('status', 'published')
            ->inRandomOrder()
            ->first();
    }

    public function sendResetLink(): void
    {
        $this->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            ['email' => $this->email]
        );

        if ($status === Password::RESET_LINK_SENT) {
            $this->statusMessage = __('reset_link_sent');
            $this->errorMessage = '';
            $this->email = '';
        } else {
            $this->errorMessage = __('reset_link_failed');
            $this->statusMessage = '';
        }
    }
};

?>

<div>
    @livewire('frontend.ui.header', ['currentTeam' => null])

    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-950">
        @if($featuredPhoto)
            <div class="fixed inset-0 opacity-10 dark:opacity-5 pointer-events-none">
                <img src="{{ $featuredPhoto->full_path ?? $featuredPhoto->thumbnail_path }}"
                     alt="Featured photography"
                     class="w-full h-full object-cover">
            </div>
        @endif

        <div class="relative max-w-md mx-auto py-20 px-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-stage-100 dark:bg-stage-900/30 mb-4">
                        <svg class="w-8 h-8 text-stage-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7.5a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM3 21v-2.25A4.5 4.5 0 017.5 14.25h3A4.5 4.5 0 0115 18.75V21"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 12h-3m0 0h-3m3 0V9m0 3v3"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">@lang('forgot_password')</h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-2 text-sm">@lang('forgot_password_instruction')</p>
                </div>

                @if($statusMessage)
                    <div class="mb-4 p-3 bg-green-100 dark:bg-green-900/20 border border-green-400 text-green-700 dark:text-green-400 rounded-lg">
                        {{ $statusMessage }}
                    </div>
                @endif

                @if($errorMessage)
                    <div class="mb-4 p-3 bg-red-100 dark:bg-red-900/20 border border-red-400 text-red-700 dark:text-red-400 rounded-lg">
                        {{ $errorMessage }}
                    </div>
                @endif

                <form wire:submit.prevent="sendResetLink" class="space-y-5">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            @lang('email_address')
                        </label>
                        <input id="email" type="email"
                               wire:model="email"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-stage-500 focus:border-transparent dark:bg-gray-700 dark:text-white @error('email') border-red-500 @enderror"
                               required autofocus>
                        @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="w-full py-3 bg-stage-600 hover:bg-stage-700 text-white font-medium rounded-lg transition-colors shadow-md hover:shadow-lg disabled:opacity-50">
                        <span wire:loading.remove>@lang('send_reset_link')</span>
                        <span wire:loading>
                            <svg class="animate-spin h-5 w-5 inline mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            @lang('sending')
                        </span>
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="text-sm text-stage-600 hover:text-stage-700">
                        ← @lang('back_to_login')
                    </a>
                </div>
            </div>
        </div>
    </div>

    @livewire('frontend.ui.footer', ['currentTeam' => null])
</div>
