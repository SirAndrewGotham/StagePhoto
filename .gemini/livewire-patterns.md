# Livewire 4 Patterns for Gemini

## Component Discovery Path
```
resources/views/components/frontend/⚡*.blade.php
```

## Standard Component Template
```blade
<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts::app')]
#[Title('Page Title')]
new class extends Component {
    // Public properties
    public $items = [];
    public $selectedItem = null;
    
    // Computed properties
    #[Computed]
    public function filteredItems()
    {
        return collect($this->items)->filter(...);
    }
    
    // Event handlers
    #[On('item-selected')]
    public function selectItem($id)
    {
        $this->selectedItem = $id;
    }
    
    // Actions
    public function save()
    {
        // Save logic
        $this->dispatch('saved');
    }
    
    public function render()
    {
        return <<<'HTML'
            <div class="container mx-auto">
                @foreach($this->filteredItems as $item)
                    <div wire:key="{{ $item['id'] }}">
                        {{ $item['name'] }}
                    </div>
                @endforeach
            </div>
        HTML;
    }
};
?>
```

## Navigation Patterns
```php
// In component
return redirect()->to('/albums');
$this->redirect('/albums', navigate: true); // SPA-style

// In routes
Route::livewire('/albums', 'frontend.album-grid');
```

## Form Handling
```blade
<?php
new class extends Component {
    public $form = [
        'name' => '',
        'email' => '',
    ];
    
    public function submit()
    {
        $this->validate([
            'form.name' => 'required|min:3',
            'form.email' => 'required|email',
        ]);
        
        // Process form
    }
};
?>
```

## Real-time Updates
```blade
<input 
    type="text" 
    wire:model.live="search" 
    placeholder="Search..."
>

<div wire:poll.5s="checkForUpdates">
    Last update: {{ $lastUpdate }}
</div>
```

## File Uploads
```blade
<?php
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;
    
    public $photo;
    
    public function save()
    {
        $this->validate([
            'photo' => 'image|max:1024',
        ]);
        
        $path = $this->photo->store('photos', 'public');
    }
};
?>
```

## Loading States
```blade
<button 
    wire:click="save" 
    wire:loading.attr="disabled"
    wire:loading.class="opacity-50"
>
    <span wire:loading.remove>Save</span>
    <span wire:loading>Saving...</span>
</button>
```
