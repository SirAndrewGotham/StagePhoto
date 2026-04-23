# 🗺️ Livewire 4 SFC Structure & Routing

## Component Discovery Path

### Component Discovery Path
Configured in `config/livewire.php`:
```php
'paths' => [
    resource_path('views/components/frontend'),
],
'component_file_naming' => 'emoji',
```

### Current File Structure
```
rresources/views/components/frontend/
├── ui/
│   ├── ⚡album-card.blade.php          ← Album card (entire card clickable)
│   ├── ⚡header.blade.php               ← Main navigation
│   ├── ⚡footer.blade.php               ← Site footer
│   ├── ⚡request-modal.blade.php        ← Request modal form
│   ├── ⚡trash-manager.blade.php        ← Trash/recycle bin manager
│   └── ⚡photo-uploader.blade.php       ← Photo upload interface
├── islands/
│   ├── ⚡filter-bar.blade.php           ← Filter bar (genre, type, sort)
│   └── ⚡album-grid.blade.php           ← Album grid island
└── pages/
    └── ⚡home.blade.php                 ← Home page
```

### 🌐 Routes

### Public Routes
| Method | URI | Component | Name |
|--------|-----|-----------|------|
| GET | `/` | `pages::home` | home |
| GET | `/albums` | `frontend.albums-index` | albums.index |
| GET | `/album/{album:slug}` | `frontend.album-show` | album.show |
| GET | `/lang/{locale}` | - | lang.switch |

### Entity Routes
| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/persona/{entity:slug}` | `persona.show` | Entity profile page (theater/band/individual) |
| GET | `/theater/{slug}` | - | Redirects to persona.show |
| GET | `/band/{slug}` | - | Redirects to persona.show |
| GET | `/artist/{slug}` | - | Redirects to persona.show |

### Protected Routes (auth required)
| Method | URI | Component | Name |
|--------|-----|-----------|------|
| GET | `/upload` | `frontend.pages.photo-upload` | photo.upload |
| GET | `/upload/multiple` | `frontend.pages.multiple-photo-upload` | photo.upload.multiple |
| GET | `/trash` | `frontend.trash-manager` | trash.manager |

### Page Components
```
resources/views/livewire/pages/
├── ⚡home.blade.php                     ← Home page (mounted at '/')
└── (Album show is in components/frontend/ui/ as it's embedded)
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

### Event Communication (Current)
| Event | Dispatched From | Listened By | Purpose |
|-------|----------------|-------------|---------|
| `open-request-modal` | Album show, Photo modal | Request modal | Open request form |
| `genre-changed` | Filter bar | Album grid | Filter albums by genre |
| `sort-changed` | Filter bar | Album grid | Sort albums |
| `type-changed` | Filter bar | Album grid | Filter by music/theater |
| `album-rated` | Album show | N/A | Refresh rating display |
| `comment-likes-updated` | Album show | N/A | Update like counts |

## Component Usage in Blade

```blade
<!-- Embedded component -->
@livewire('frontend.ui.album-card', ['album' => $album])

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
