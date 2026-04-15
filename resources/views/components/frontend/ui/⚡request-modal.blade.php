<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Request;

new class extends Component {
    public $albumId;
    public $showModal = false;

    // Form fields
    public $requester_name = '';
    public $requester_email = '';
    public $requester_phone = '';
    public $request_type = 'personal_use';
    public $message = '';
    public $event_date = '';
    public $event_venue = '';
    public $budget_range = '';

    protected $rules = [
        'requester_name' => 'required|string|min:2|max:255',
        'requester_email' => 'required|email|max:255',
        'requester_phone' => 'nullable|string|max:20',
        'request_type' => 'required|in:hire_photographer,high_res_photos,print_permission,commercial_use,personal_use',
        'message' => 'required|string|min:10|max:5000',
        'event_date' => 'nullable|date',
        'event_venue' => 'nullable|string|max:255',
        'budget_range' => 'nullable|string|max:100',
    ];

    public function mount(): void
    {
        if (auth()->check()) {
            $this->requester_name = auth()->user()->name;
            $this->requester_email = auth()->user()->email;
        }
    }

    #[On('open-request-modal')]
    public function handleOpenRequestModal($albumId): void
    {
        $this->albumId = $albumId;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->albumId = null;
        $this->reset([
            'requester_name', 'requester_email', 'requester_phone',
            'request_type', 'message', 'event_date', 'event_venue', 'budget_range'
        ]);

        if (auth()->check()) {
            $this->requester_name = auth()->user()->name;
            $this->requester_email = auth()->user()->email;
        }
    }

    public function submit(): void
    {
        $this->validate();

        Request::create([
            'album_id' => $this->albumId,
            'user_id' => auth()->id(),
            'requester_name' => $this->requester_name,
            'requester_email' => $this->requester_email,
            'requester_phone' => $this->requester_phone,
            'request_type' => $this->request_type,
            'message' => $this->message,
            'event_date' => $this->event_date,
            'event_venue' => $this->event_venue,
            'budget_range' => $this->budget_range,
            'status' => 'pending',
        ]);

        session()->flash('success', 'Your request has been sent! The photographer will contact you soon.');

        $this->closeModal();

        $this->dispatch('request-submitted');
    }
};

?>

<div>
    <!-- Modal - using simple Alpine.js with x-show -->
    <div x-data="{ open: false }"
         x-on:open-request-modal.window="open = true"
         x-show="open"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">

        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/50 transition-opacity" @click="open = false"></div>

        <!-- Modal Panel -->
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="relative max-w-2xl w-full bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Request Information
                    </h2>
                    <button
                        @click="open = false"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form wire:submit="submit" class="p-6">
                    <div class="space-y-4">
                        <!-- Request Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Request Type *
                            </label>
                            <select wire:model="request_type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-stage-500 focus:border-transparent dark:bg-gray-700">
                                <option value="personal_use">📸 Personal Use (wallpaper, social media)</option>
                                <option value="high_res_photos">🖼️ High Resolution Photos</option>
                                <option value="print_permission">🖨️ Print Permission</option>
                                <option value="commercial_use">💼 Commercial Use (website, advertising)</option>
                                <option value="hire_photographer">🎯 Hire Photographer for Event</option>
                            </select>
                            @error('request_type') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Your Name *
                            </label>
                            <input type="text" wire:model="requester_name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-stage-500 focus:border-transparent dark:bg-gray-700" placeholder="John Doe">
                            @error('requester_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Email Address *
                            </label>
                            <input type="email" wire:model="requester_email" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-stage-500 focus:border-transparent dark:bg-gray-700" placeholder="you@example.com">
                            @error('requester_email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Phone (optional) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Phone Number (optional)
                            </label>
                            <input type="tel" wire:model="requester_phone" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-stage-500 focus:border-transparent dark:bg-gray-700" placeholder="+1 (555) 000-0000">
                        </div>

                        <!-- Event Date (for hire requests) -->
                        <div x-show="$wire.request_type === 'hire_photographer'" x-cloak>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Event Date
                            </label>
                            <input type="date" wire:model="event_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-stage-500 focus:border-transparent dark:bg-gray-700">
                        </div>

                        <!-- Event Venue (for hire requests) -->
                        <div x-show="$wire.request_type === 'hire_photographer'" x-cloak>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Event Venue
                            </label>
                            <input type="text" wire:model="event_venue" placeholder="Venue name and location" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-stage-500 focus:border-transparent dark:bg-gray-700">
                        </div>

                        <!-- Budget Range (for hire requests) -->
                        <div x-show="$wire.request_type === 'hire_photographer'" x-cloak>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Budget Range
                            </label>
                            <select wire:model="budget_range" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-stage-500 focus:border-transparent dark:bg-gray-700">
                                <option value="">Select budget range</option>
                                <option value="under_500">Under $500</option>
                                <option value="500_1000">$500 - $1,000</option>
                                <option value="1000_2000">$1,000 - $2,000</option>
                                <option value="2000_5000">$2,000 - $5,000</option>
                                <option value="over_5000">Over $5,000</option>
                            </select>
                        </div>

                        <!-- Message -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Message *
                            </label>
                            <textarea wire:model="message" rows="4" placeholder="Tell the photographer what you need..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-stage-500 focus:border-transparent dark:bg-gray-700"></textarea>
                            @error('message') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" @click="open = false" class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="px-4 py-2 bg-stage-600 text-white rounded-lg hover:bg-stage-700 transition-colors disabled:opacity-50">
                            <span wire:loading.remove>Send Request</span>
                            <span wire:loading>
                                <svg class="animate-spin inline h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Sending...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
