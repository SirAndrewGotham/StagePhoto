# AGENTS.md - StagePhoto.ru AI Assistant Guide

## Project Overview
- **Framework**: Laravel 13
- **Frontend**: Livewire 4 (SPC - Single Page Components)
- **Styling**: Tailwind CSS 4
- **JavaScript**: Alpine.js 3 for client-side state
- **PHP Version**: 8.2+

## Key Directories
- `app/Livewire/` - Livewire class components (if any)
- `resources/views/components/frontend/` - Livewire 4 SFC with ⚡ prefix
- `resources/views/layouts/` - Blade layouts
- `resources/views/pages/` - Full-page Livewire components
- `config/` - Configuration files (especially `livewire.php`)
- `routes/web.php` - Route definitions with `Route::livewire()`

## Development Commands
```bash
# Create Livewire SFC
php artisan livewire:make frontend/ComponentName --type=sfc

# Run tests
php artisan test

# Run Rector (code refactoring)
vendor/bin/rector process app --dry-run

# Run Pint (code styling)
./vendor/bin/pint

# Start development server
php artisan serve
```

## AI Assistant Responsibilities
1. Generate Livewire 4 SFC components following the project pattern
2. Write tests for Livewire components
3. Suggest performance optimizations for computed properties
4. Ensure proper event handling between components
5. Maintain dark mode and translation consistency

## Code Generation Rules
- Always use `new class extends Component { }` syntax
- Include proper PHP imports at top of SFC
- Use `#[Computed]` for expensive operations
- Use `#[On]` for event listeners
- Never mix Folio routing (it's been removed)

## Translation Keys Reference
- Available in Alpine.js `t()` function
- Languages: `ru`, `en`, `eo`
- Add new keys to all three language objects

## Dark Mode Implementation
- Toggled via Alpine.js `darkMode` property
- Class `dark` added to `<html>` element
- Use `dark:` variant in Tailwind classes
- Persists to localStorage

## Testing Requirements
- Test Livewire components with `Livewire::test()`
- Test Alpine.js interactions separately
- Test dark mode persistence
- Test translation switching
