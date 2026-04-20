<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use App\Models\User;
use App\Models\Photo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

new class extends Component {
    public $name = '';
    public $email = '';
    public $password = '';
    public $passwordConfirmation = '';
    public $agreeTerms = false;
    public $featuredPhoto;

    #[Title('Create Account - StagePhoto.ru')]
    public function mount(): void
    {
        $this->featuredPhoto = Photo::with('album.photographer')
            ->where('status', 'published')
            ->inRandomOrder()
            ->first();
    }

    public function register(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|same:passwordConfirmation',
            'agreeTerms' => 'accepted',
        ], [
            'email.unique' => __('email_unique'),
            'password.min' => __('password_min'),
            'password.same' => __('password_confirmation'),
            'agreeTerms.accepted' => __('agree_terms_required'),
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        Auth::login($user);
        session()->regenerate();

        $this->redirect(route('albums.index'));
    }

    public function getMembershipBenefitsProperty(): array
    {
        return [
            ['icon' => '🎫', 'title' => __('free_portfolio'), 'description' => __('free_portfolio_desc')],
            ['icon' => '🌟', 'title' => __('get_featured'), 'description' => __('get_featured_desc')],
            ['icon' => '💬', 'title' => __('community_access'), 'description' => __('community_access_desc')],
            ['icon' => '📊', 'title' => __('analytics'), 'description' => __('analytics_desc')],
            ['icon' => '🏅', 'title' => __('monthly_contests'), 'description' => __('contests_desc')],
            ['icon' => '🔗', 'title' => __('networking'), 'description' => __('networking_desc')],
        ];
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

        <div class="relative max-w-6xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-8 min-h-[calc(100vh-6rem)]">
                <!-- Left Column - Benefits -->
                <div class="hidden lg:block space-y-6">
                    <div class="bg-gradient-to-br from-stage-50 to-orange-50 dark:from-stage-900/20 dark:to-orange-900/20 rounded-2xl p-8">
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">@lang('join_stagephoto')</h2>
                        <p class="text-gray-600 dark:text-gray-300 mb-6">@lang('become_part_community')</p>

                        <div class="grid grid-cols-2 gap-4">
                            @foreach($this->membershipBenefits as $benefit)
                                <div class="flex items-start gap-2">
                                    <span class="text-lg">{{ $benefit['icon'] }}</span>
                                    <div>
                                        <h3 class="font-semibold text-gray-900 dark:text-white text-xs">{{ $benefit['title'] }}</h3>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ $benefit['description'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-xl p-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400 text-center">
                            📸 @lang('i_agree_to') <a href="#" class="text-stage-600 hover:underline">@lang('terms_of_service')</a> & <a href="#" class="text-stage-600 hover:underline">@lang('privacy_policy')</a>
                        </p>
                    </div>
                </div>

                <!-- Right Column - Registration Form -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
                    <div class="text-center lg:hidden mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">@lang('join_stagephoto')</h2>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">@lang('become_part_community')</p>
                    </div>

                    <form wire:submit.prevent="register" class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                @lang('full_name')
                            </label>
                            <input id="name" type="text"
                                   wire:model="name"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-stage-500 focus:border-transparent dark:bg-gray-700 dark:text-white @error('name') border-red-500 @enderror"
                                   required>
                            @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                @lang('email_address')
                            </label>
                            <input id="email" type="email"
                                   wire:model="email"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-stage-500 focus:border-transparent dark:bg-gray-700 dark:text-white @error('email') border-red-500 @enderror"
                                   required>
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
                            <p class="text-xs text-gray-500 mt-1">@lang('minimum_8_characters')</p>
                        </div>

                        <div>
                            <label for="passwordConfirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                @lang('confirm_password')
                            </label>
                            <input id="passwordConfirmation" type="password"
                                   wire:model="passwordConfirmation"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-stage-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                   required>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" wire:model="agreeTerms"
                                   class="rounded border-gray-300 text-stage-600 focus:ring-stage-500">
                            <label class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                @lang('i_agree_to') <a href="#" class="text-stage-600 hover:underline">@lang('terms_of_service')</a> @lang('and') <a href="#" class="text-stage-600 hover:underline">@lang('privacy_policy')</a>
                            </label>
                        </div>
                        @error('agreeTerms')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror

                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="w-full py-3 bg-stage-600 hover:bg-stage-700 text-white font-medium rounded-lg transition-colors shadow-md hover:shadow-lg disabled:opacity-50">
                            <span wire:loading.remove>@lang('create_account_button')</span>
                            <span wire:loading>
                                <svg class="animate-spin h-5 w-5 inline mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                @lang('creating_account')
                            </span>
                        </button>
                    </form>

                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            @lang('already_have_account')
                            <a href="{{ route('login') }}" class="text-stage-600 hover:text-stage-700 font-medium">
                                @lang('sign_in_here')
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @livewire('frontend.ui.footer', ['currentTeam' => null])
</div>
