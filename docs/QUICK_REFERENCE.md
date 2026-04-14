# StagePhoto.ru Quick Reference for AI

## Project
- Laravel 13 + Livewire 4 + Tailwind 4 + Alpine 3
- Repo: github.com/SirAndrewGotham/StagePhoto

## File Locations
| Type | Path |
|------|------|
| Livewire SFC | `resources/views/components/frontend/⚡*.blade.php` |
| Layouts | `resources/views/layouts/app.blade.php` |
| Routes | `routes/web.php` (use `Route::livewire()`) |

## Critical Rules
- ❌ No `container`, `max-w-*`, or Spatie packages
- ✅ Full-width grid: `grid-cols-(auto-fill, minmax(280px, 1fr))`
- ✅ Dark mode: `$store.theme.dark` (Alpine)
- ✅ Translations: `$store.i18n.t('key')` (Alpine)

## Livewire 4 Syntax
```php
#[Url] public $search = '';
#[Computed] public function albums() { }
#[On('event')] public function handler() { }
$this->dispatch('event', data: $value);
```

## Component Template
```blade
<?php
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;

new class extends Component {
    #[Url]
    public $property = '';
    
    #[Computed]
    public function data() { return []; }
    
    public function render()
    {
        return <<<'HTML'
            <div class="full-width">
                <!-- Template -->
            </div>
        HTML;
    }
};
?>
```
