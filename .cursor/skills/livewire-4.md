# Livewire 4 Development Skills for StagePhoto.ru

## Project-Specific Knowledge

### Component Location
- **Path**: `resources/views/components/frontend/`
- **Naming**: `⚡component-name.blade.php` (emoji prefix for SFC)
- **Example**: `resources/views/components/frontend/⚡album-card.blade.php`

### Livewire 4 Patterns Used
- **Anonymous Class Components**: `new class extends Component { }`
- **Computed Properties**: `#[Computed]` attribute for memoized data
- **Event Listeners**: `#[On('event-name')]` attribute
- **Event Dispatch**: `$this->dispatch('event-name', data: $value)`

### Component Structure Template
```blade
<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

new class extends Component {
    public $property;
    
    #[Computed]
    public function computedData()
    {
        return // memoized value;
    }
    
    #[On('some-event')]
    public function handleEvent($data)
    {
        // handle event
    }
    
    public function render()
    {
        return <<<'HTML'
            <div>
                <!-- Template HTML -->
            </div>
        HTML;
    }
};
?>
```

### Routes Configuration
```php
// routes/web.php
Route::livewire('/', 'pages::home');
Route::livewire('/album/{id}', 'pages::album-show');
```

### Testing Livewire Components
```bash
php artisan make:livewire-test AlbumCard
```

## Custom Configuration

### Livewire Config (`config/livewire.php`)
```php
'paths' => [
    resource_path('views/components/frontend'),
    resource_path('views/livewire'),
],
'component_file_naming' => 'emoji',
```

### Tailwind Custom Colors
```css
stage-50: '#fff8f0'
stage-100: '#ffedd5'
stage-500: '#f97316'
stage-600: '#ea580c'
stage-900: '#1e1b4b'
```

## Development Workflow

1. **Create new component**:
   ```bash
   php artisan livewire:make frontend/ComponentName --type=sfc
   ```

2. **Register in routes** (if full-page):
   ```php
   Route::livewire('/path', 'frontend.component-name');
   ```

3. **Use in Blade**:
   ```blade
   @livewire('frontend.component-name')
   @livewire('frontend.component-name', ['prop' => $value])
   ```

## Known Issues & Solutions

- **Issue**: Livewire not finding components in custom path
  **Solution**: Verify `config/livewire.php` has the correct path registered

- **Issue**: Event listeners not firing
  **Solution**: Ensure `#[On]` attribute is imported and method is public

- **Issue**: Computed properties not updating
  **Solution**: Use `#[Computed]` attribute and access as `$this->computedProperty`
