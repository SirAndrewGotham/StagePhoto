<?php

use Livewire\Component;

new class extends Component {
    public $rating = 0;
    public $average = 0;
    public $count = 0;
    public $size = 'text-xl';

    public function mount($average = 0, $count = 0, $userRating = 0, $size = 'text-xl'): void
    {
        $this->average = $average;
        $this->count = $count;
        $this->rating = $userRating;
        $this->size = $size;
    }

    public function rate($rating): void
    {
        if (!auth()->check()) {
            $this->dispatch('show-login-modal');
            return;
        }

        $this->dispatch('rate', rating: $rating);
        $this->rating = $rating;
    }
};

?>

<div x-data="{ rating: {{ $rating }}, hoverRating: 0, average: {{ $average }} }"
     x-on:rating-updated.window="rating = $event.detail.rating; average = $event.detail.average">
    <div class="flex items-center gap-2">
        <div class="flex items-center gap-0.5">
            <template x-for="star in [1,2,3,4,5]">
                <button
                    @click="$wire.rate(star)"
                    @mouseenter="hoverRating = star"
                    @mouseleave="hoverRating = 0"
                    class="{{ $size }} transition-colors focus:outline-none"
                    :class="{
                        'text-yellow-400': (hoverRating ? star <= hoverRating : star <= (rating || average)),
                        'text-gray-300 dark:text-gray-600': !(hoverRating ? star <= hoverRating : star <= (rating || average))
                    }"
                >
                    ★
                </button>
            </template>
        </div>
        <span class="text-sm">
            {{ number_format($average, 1) }} ({{ $count }})
        </span>
    </div>
</div>
