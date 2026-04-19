<?php

use Livewire\Component;

new class extends Component {
    public $currentRoute = '';
    public $activeTab = '';

    public $tabs = [
        'photo.upload' => ['icon' => '📷', 'label' => 'Single Photo'],
        'photo.upload.multiple' => ['icon' => '📸', 'label' => 'Multiple Photos'],
        'photo.upload.zip' => ['icon' => '🗜️', 'label' => 'ZIP Archive'],
    ];

    public function mount()
    {
        $this->currentRoute = request()->route()->getName();
        $this->activeTab = $this->currentRoute;
    }
};

?>

<div class="border-b border-gray-200 dark:border-gray-700 mb-6">
    <div class="flex flex-wrap gap-1">
        @foreach($tabs as $routeName => $tab)
            <a href="{{ route($routeName) }}"
               class="px-4 py-2 text-sm font-medium rounded-t-lg transition-all duration-200 flex items-center gap-2
                   {{ $activeTab === $routeName
                       ? 'bg-stage-600 text-white shadow-md'
                       : 'text-gray-600 dark:text-gray-400 hover:text-stage-600 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                <span class="text-lg">{{ $tab['icon'] }}</span>
                <span>{{ $tab['label'] }}</span>
            </a>
        @endforeach
    </div>
</div>
