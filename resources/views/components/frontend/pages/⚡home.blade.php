<?php

use Livewire\Component;

new class extends Component {
//    public function render()
//    {
//        return view('layouts::app');
//    }
};

?>

<div>
    @livewire('ui.header')
    @livewire('islands.filter-bar')
    <main>
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            @livewire('islands.albums-grid')
        </div>
    </main>
    @livewire('ui.footer')
</div>
