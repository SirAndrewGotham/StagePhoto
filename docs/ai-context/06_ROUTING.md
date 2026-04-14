# 🗺️ Livewire 4 SFC Structure & Routing

## Component Discovery Path

Configured in `config/livewire.php`:

```php
'paths' => [
    resource_path('views/components/frontend'),
],
'component_file_naming' => 'emoji', // ⚡ prefix for SFC
```

## Actual File Structure

```
resources/views/components/frontend/
├── ⚡header.blade.php              ← Header SFC
├── ⚡filter-bar.blade.php          ← Filter bar SFC  
├── ⚡album-grid.blade.php          ← Album grid island
├── ⚡album-card.blade.php          ← Album card UI component
├── ⚡footer.blade.php              ← Footer SFC
└── ⚡request-modal.blade.php       ← Request modal SFC (when created)
```

## Page Components (Full-page Livewire)

```
resources/views/livewire/pages/
├── ⚡home.blade.php                ← Home page (mounted at '/')
└── ⚡album-show.blade.php          ← Album detail page
```

## Route Registration

```php
// routes/web.php
use Livewire\Livewire;

// Full-page components
Route::livewire('/', 'pages::home');
Route::livewire('/album/{slug}', 'pages::album-show');

// Embedded components (no route needed)
// Used via @livewire('frontend.album-grid')
```

## Component Usage in Blade

```blade
<!-- Embedded component -->
@livewire('frontend.album-card', ['album' => $album])

<!-- With explicit key for loops -->
@livewire('frontend.album-card', ['album' => $album], key($album['id']))

<!-- Full-page component -->
@livewire('pages::home')
```

## 📛 Component Naming Conventions

### File Naming
- **SFC components**: `⚡{kebab-case}.blade.php`
    - Example: `⚡album-card.blade.php`
- **Page components**: `⚡{kebab-case}.blade.php` in `livewire/pages/`
    - Example: `⚡home.blade.php`

### Route Registration with Parameters
```php
// Simple
Route::livewire('/albums', 'pages::albums-index');

// With parameters
Route::livewire('/album/{slug}', 'pages::album-show');

// With layout specification
Route::livewire('/dashboard', 'pages::dashboard')->layout('layouts.admin');

// With middleware
Route::livewire('/dashboard', 'pages::dashboard')->middleware('auth');
```

## 🔗 URL Rules
- Slugs: lowercase, hyphenated, unique (e.g., `arctic-monkeys-live-luzhniki`)
- Photographer: `/photographer/{username}` (not `@username` for SEO)
- Static pages: `/about`, `/faq`, `/privacy` (footer links only)

## 📦 Livewire Islands (Embedded Components)
- `frontend.album-grid` → handles pagination, infinite scroll
- `frontend.filter-bar` → syncs with URL params via `#[Url]`
- `frontend.request-modal` → booking flow, validation, notifications
- `frontend.header` → navigation, theme, language (Alpine.js managed)

## 🧪 Creating New Components

```bash
# Create SFC component
php artisan livewire:make frontend/component-name --type=sfc

# Create page component
php artisan livewire:make pages/page-name --type=sfc

# Create with custom namespace
php artisan livewire:make frontend/ui/button --type=sfc
```
