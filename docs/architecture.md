# StagePhoto.ru Architecture Documentation

## Technology Stack

### Backend
- **Laravel 13** - PHP Framework
- **Livewire 4** - Full-stack framework for dynamic interfaces
- **Rector** - Automated code refactoring
- **Pint** - Code style fixing

### Frontend
- **Livewire 4 SFC** - Single-File Components with ⚡ prefix
- **Tailwind CSS 4** - Utility-first CSS framework
- **Alpine.js 3** - Lightweight JavaScript framework
- **No build step** - CDN-based approach

## Component Architecture

### Livewire Components Structure
```
resources/views/components/frontend/
├── ⚡header.blade.php          # Navigation, theme, language
├── ⚡filter-bar.blade.php      # Genre filters, sorting
├── ⚡album-grid.blade.php      # Masonry grid container
├── ⚡album-card.blade.php      # Individual album preview
└── ⚡footer.blade.php          # Footer links
```

### State Management
| State Type | Technology | Scope |
|------------|-----------|-------|
| Dark Mode | Alpine.js | Global |
| Language | Alpine.js | Global |
| Album Data | Livewire | Server-side |
| Filters | Livewire | Server-side |
| UI Loading | Livewire | Component |

### Data Flow
1. User interacts with Alpine.js UI (language, theme)
2. Alpine dispatches custom events
3. Livewire components listen to events
4. Livewire fetches/updates data
5. Livewire re-renders affected components
6. Alpine.js updates client-side state

## Routing Strategy

### Full-Page Components (Livewire)
```php
Route::livewire('/', 'pages::home');
Route::livewire('/albums', 'frontend.album-grid');
Route::livewire('/album/{id}', 'pages::album-show');
```

### Embedded Components
```blade
@livewire('frontend.filter-bar')
@livewire('frontend.album-card', ['album' => $album])
```

## Translation System

### Implementation
- Client-side translations in Alpine.js
- Three languages: Russian, English, Esperanto
- Language change dispatches `language-changed` event

### Adding New Translations
1. Add key to all language objects in `t()` function
2. Ensure key exists in ru, en, and eo objects
3. Test with language switcher

## Dark Mode Implementation

### How It Works
- Alpine.js `darkMode` property (boolean)
- `dark` class toggled on `<html>` element
- Tailwind `dark:` variant for styling
- Persists to localStorage

### Adding Dark Mode Styles
```blade
<div class="bg-white dark:bg-gray-800">
    <h1 class="text-gray-900 dark:text-white">Title</h1>
</div>
```

## Performance Optimizations

### Livewire 4 Features Used
- `#[Computed]` for expensive operations
- `wire:key` for list rendering
- `wire:model.live` only when needed
- Lazy loading for images

### Best Practices
- Avoid storing Eloquent models in public properties
- Use IDs instead of full models
- Paginate large datasets
- Use `defer` for non-critical updates

## Testing Strategy

### Test Types
- **Feature Tests** - Full page interactions
- **Livewire Tests** - Component behavior
- **Unit Tests** - Business logic

### Example Livewire Test
```php
test('album grid loads albums', function () {
    Livewire::test('frontend.album-grid')
        ->assertViewHas('albums')
        ->assertSee('Arctic Monkeys');
});
```

## Deployment

### Requirements
- PHP 8.2+
- Composer
- Node.js (only for Vite if used)

### Production Commands
```bash
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Future Considerations

### Potential Upgrades
- Move to Vite for asset bundling (if build step needed)
- Add Redis for caching
- Implement queue for image processing
- Add API endpoints for mobile app

### Scalability Concerns
- Livewire component size (keep under 200 lines)
- Database query optimization
- Image CDN integration
