<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="w-full min-h-screen bg-white dark:bg-stage-900 transition-colors duration-200">
    {{-- 🔝 Header --}}
    <x-frontend.ui.header />

    {{-- 🖼️ Full-Width Grid --}}
    <x-frontend.islands.albums-grid />

    {{-- 🦶 Footer --}}
    <x-frontend.ui.footer />
</div>
