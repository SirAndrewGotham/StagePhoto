<?php

use Livewire\Component;

new class extends Component {
    public $currentTeam;

    public function mount($currentTeam = null): void
    {
        $this->currentTeam = $currentTeam;
    }
};

?>

<div>
    @livewire('frontend.ui.header', ['currentTeam' => $currentTeam])
    @livewire('frontend.islands.filter-bar')

    <div class="max-w-2xl mx-auto py-8 px-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">@lang('upload_zip_archive')</h1>

        @livewire('frontend.ui.uploads-tab-navigation')

        @livewire('frontend.ui.upload-form', ['uploadType' => 'zip'], key('zip-upload'))
    </div>

    @livewire('frontend.ui.footer', ['currentTeam' => $currentTeam])
</div>
