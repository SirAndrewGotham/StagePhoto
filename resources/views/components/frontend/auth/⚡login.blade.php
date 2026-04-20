<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use App\Models\Photo;
use App\Models\Album;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

new class extends Component {
    public $email = '';
    public $password = '';
    public $remember = false;
    public $featuredPhoto;

    #[Title('Sign In - StagePhoto.ru')]
    public function mount(): void
    {
        $this->featuredPhoto = Photo::with('album.photographer')
            ->where('status', 'published')
            ->inRandomOrder()
            ->first();
    }

    public function login(): void
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $throttleKey = Str::lower($this->email) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', __('too_many_attempts', ['seconds' => $seconds]));
            return;
        }

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::clear($throttleKey);
            session()->regenerate();
            $this->redirectIntended(default: route('albums.index'));
        } else {
            RateLimiter::hit($throttleKey);
            $this->addError('email', __('invalid_credentials'));
        }
    }

    public function getCommunityBenefitsProperty(): array
    {
        return [
            [
                'icon' => '📸',
                'title' => __('showcase_title'),
                'description' => __('showcase_desc')
            ],
            [
                'icon' => '🏆',
                'title' => __('contests_title'),
                'description' => __('contests_desc')
            ],
            [
                'icon' => '🤝',
                'title' => __('connect_title'),
                'description' => __('connect_desc')
            ],
            [
                'icon' => '📈',
                'title' => __('growth_title'),
                'description' => __('growth_desc')
            ],
        ];
    }
};

?>

<div>
    @livewire('frontend.ui.header', ['currentTeam' => null])

    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-950">
        <!-- Background Photo -->
        @if($featuredPhoto)
            <div class="fixed inset-0 opacity-10 dark:opacity-5 pointer-events-none">
                <img src="{{ $featuredPhoto->full_path ?? $featuredPhoto->thumbnail_path }}"
                     alt="Featured photography"
                     class="w-full h-full object-cover">
            </div>
        @endif

        <div class="relative max-w-6xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-8 items-center min-h-[calc(100vh-6rem)]">
                <!-- Left Column - Benefits & Info -->
                <div class="hidden lg:block space-y-6">
                    <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl p-8 shadow-xl">
                        <div class="text-center mb-6">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-stage-100 dark:bg-stage-900/30 mb-4">
                                <svg class="w-8 h-8 text-stage-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">@lang('welcome_back')</h2>
                            <p class="text-gray-600 dark:text-gray-400 mt-2">@lang('sign_in_to_continue')</p>
                        </div>

                        <div class="space-y-4">
                            @foreach($this->communityBenefits as $benefit)
                                <div class="flex items-start gap-3">
                                    <span class="text-xl">{{ $benefit['icon'] }}</span>
                                    <div>
                                        <h3 class="font-semibold text-gray-900 dark:text-white text-sm">{{ $benefit['title'] }}</h3>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ $benefit['description'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($featuredPhoto && $featuredPhoto->album)
                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                                    @lang('featured_photo_from')<br>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ $featuredPhoto->album->title }}</span>
                                    <br>@lang('by_photographer') {{ $featuredPhoto->album->photographer->name ?? 'StagePhoto Community' }}
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-xl p-4 text-center">
                        <a href="#" class="text-sm text-gray-600 dark:text-gray-400 hover:text-stage-600 transition">
                            📖 @lang('read_community_guidelines')
                        </a>
                    </div>
                </div>

                <!-- Right Column - Login Form -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
                    <div class="text-center lg:hidden mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">@lang('welcome_back')</h2>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">@lang('sign_in_to_continue')</p>
                    </div>

                    <form wire:submit.prevent="login" class="space-y-5">
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

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                @lang('password')
                            </label>
                            <input id="password" type="password"
                                   wire:model="password"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-stage-500 focus:border-transparent dark:bg-gray-700 dark:text-white @error('password') border-red-500 @enderror"
                                   required>
                            @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="remember" class="rounded border-gray-300 text-stage-600 focus:ring-stage-500">
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">@lang('remember_me')</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm text-stage-600 hover:text-stage-700">
                                    @lang('forgot_password')
                                </a>
                            @endif
                        </div>

                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="w-full py-3 bg-stage-600 hover:bg-stage-700 text-white font-medium rounded-lg transition-colors shadow-md hover:shadow-lg disabled:opacity-50">
                            <span wire:loading.remove>@lang('sign_in')</span>
                            <span wire:loading>
                                <svg class="animate-spin h-5 w-5 inline mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                @lang('signing_in')
                            </span>
                        </button>
                    </form>

                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            @lang('dont_have_account')
                            <a href="{{ route('register') }}" class="text-stage-600 hover:text-stage-700 font-medium">
                                @lang('create_account')
                            </a>
                        </p>
                    </div>

                    <div class="mt-6">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400">@lang('or_continue_with')</span>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-center space-x-4">
                            <button class="text-gray-400 hover:text-stage-600 transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                                </svg>
                            </button>
                            <button class="text-gray-400 hover:text-stage-600 transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 16.133 0 12.48 0 5.867 0 .307 5.387.307 12s5.56 12 12.173 12c3.573 0 6.267-1.173 8.373-3.36 2.16-2.16 2.84-5.213 2.84-7.667 0-.76-.053-1.467-.173-2.053H12.48z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @livewire('frontend.ui.footer', ['currentTeam' => null])
</div>
