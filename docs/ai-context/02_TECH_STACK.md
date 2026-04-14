# 🛠️ Technical Stack & Architecture

## 📦 Core Framework
- **PHP**: 8.4+
- **Laravel**: 13.x
- **Livewire**: 4.x (Single-File Components with ⚡ prefix)
- **Tailwind**: 4.x
- **Frontend**: Livewire 4 + Alpine.js 3
- **Styling**: Tailwind CSS 4 (CDN-based, no build step)
- **DB**: MySQL 8+ or PostgreSQL 15+
- **Cache/Queue**: Redis (optional for production)

## 🧩 Livewire 4 Key Patterns

### URL Synchronization with `#[Url]`
```php
use Livewire\Attributes\Url;

new class extends Component {
    #[Url(as: 'page', history: true)]
    public $currentPage = 1;
    
    #[Url(filters: ['rock', 'metal', 'theater'])] 
    public $genre = 'all';
    
    #[Url(keep: false)]
    public $search = '';
    
    #[Url(except: '')]
    public $query = '';
}
```

### Real-time Updates with `wire:model`
```blade
<!-- Debounced input (300ms default) -->
<input 
    type="text" 
    wire:model.live.debounce.300ms="search" 
    placeholder="Search bands..."
>

<!-- Lazy loading for expensive operations -->
<select wire:model.live.lazy="sortBy">
    <option value="recent">Most Recent</option>
    <option value="views">Most Viewed</option>
    <option value="rating">Top Rated</option>
</select>

<!-- Blur trigger -->
<input wire:model.blur="validateField">
```

### Computed Properties with `#[Computed]`
```php
use Livewire\Attributes\Computed;

new class extends Component {
    #[Computed]
    public function filteredAlbums()
    {
        return Album::query()
            ->when($this->genre, fn($q) => $q->where('genre', $this->genre))
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(12);
    }
    
    #[Computed(persist: true)] // Persists across requests
    public function userPreferences()
    {
        return auth()->user()?->preferences ?? [];
    }
}
```

### Event Handling with `#[On]`
```php
use Livewire\Attributes\On;

new class extends Component {
    #[On('language-changed')]
    public function updateLanguage($language)
    {
        app()->setLocale($language);
        $this->reset();
        $this->dispatch('$refresh');
    }
    
    #[On('album-created')]
    public function handleNewAlbum($albumId)
    {
        $this->albums->prepend(Album::find($albumId));
    }
}
```

### Event Dispatching
```php
// Dispatch to parent/other components
$this->dispatch('genre-changed', genre: $this->selectedGenre);

// Dispatch with multiple parameters
$this->dispatch('filter-updated', genre: $genre, sort: $sort, page: 1);

// Dispatch from Alpine to Livewire
window.dispatchEvent(new CustomEvent('language-changed', { 
    detail: { language: 'en' } 
}));
```

## ⚙️ Livewire 4 Configuration

### `config/livewire.php` (Critical Settings)
```php
return [
    // Component discovery paths (order matters)
    'paths' => [
        resource_path('views/components/frontend'),
        resource_path('views/livewire'),
    ],
    
    // SFC with emoji prefix (⚡)
    'component_file_naming' => 'emoji',
    
    // Class namespace for components
    'components_namespace' => 'App\\Livewire',
    
    // View paths for component rendering
    'view_paths' => [
        resource_path('views/components/frontend'),
        resource_path('views/livewire'),
    ],
    
    // Navigation mode (enables SPA-style navigation)
    'navigate' => true,
    
    // Inject assets automatically
    'inject_assets' => true,
    
    // Scripts and styles injection position
    'inject_assets_before' => false,
];
```

## 🚫 Banned Technologies
- jQuery, Vue, React, Inertia, Bootstrap
- Custom CSS files (unless inside `@layer` directives)
- `container`, `max-w-*` classes on main layout sections
- Client-side routing for album browsing (use Livewire SSR + Alpine)
- Spatie packages (per project policy)

## ✅ Performance Rules
- Debounce all filters: `wire:model.live.debounce.300ms`
- Paginate albums via Livewire: 12 per page
- Use CSS grid: `grid-template-columns: repeat(auto-fill, minmax(280px, 1fr))`
- Preload critical fonts, defer Alpine/Livewire scripts
- Lazy load images: `loading="lazy" decoding="async"`

## 🧪 Testing Livewire Components

```bash
# Generate test for component
php artisan make:livewire-test frontend/album-grid

# Run all Livewire tests
php artisan test --filter=Livewire

# Run specific test
php artisan test --filter=AlbumGridTest
```

### Example Test
```php
<?php

use Livewire\Livewire;

test('album grid loads albums', function () {
    Livewire::test('frontend.album-grid')
        ->assertViewHas('albums')
        ->assertSee('Arctic Monkeys');
});

test('filters by genre', function () {
    Livewire::test('frontend.album-grid')
        ->set('selectedGenre', 'rock')
        ->assertSet('selectedGenre', 'rock')
        ->assertSee('Arctic Monkeys');
});
```

## 🐛 Debugging Livewire

```php
// In component - dump all properties
dd($this->all());

// In component - dump specific property
dd($this->search);

// In blade - debug component state
@json($this->all())

// Browser console
Livewire.dump()
```

## 📊 Performance Budget

| Metric | Target | Measurement |
|--------|--------|-------------|
| First Contentful Paint (FCP) | < 1.0s | Lighthouse |
| Largest Contentful Paint (LCP) | < 2.0s | Lighthouse |
| Time to Interactive (TTI) | < 2.5s | Lighthouse |
| Cumulative Layout Shift (CLS) | < 0.1 | Lighthouse |
| Total JS Size (Livewire + Alpine) | < 100KB | Browser DevTools |
| First CPU Idle | < 1.5s | Lighthouse |

### Livewire 4 Optimizations
```blade
<!-- Lazy load off-screen components -->
<div x-intersect="$wire.load()">
    @livewire('heavy-component', lazy: true)
</div>

<!-- Defer non-critical Livewire components -->
@livewire('comments-section', defer: true)

<!-- Use wire:key for stable lists -->
@foreach($albums as $album)
    <div wire:key="album-{{ $album['id'] }}">
        {{ $album['title'] }}
    </div>
@endforeach
```
