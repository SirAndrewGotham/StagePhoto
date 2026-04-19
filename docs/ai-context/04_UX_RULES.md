# 🖱️ UX Rules & Interaction Patterns

## 🧭 Filter Bar
- Sticky: `top-16 z-40`
- Genre pills: horizontal scrollable, active = `bg-stage-600`
- Sort dropdown: `mostRecent`, `mostViewed`, `topRated`, `newPhotographers`
- Debounce: `wire:model.live.debounce.300ms`
- Mobile: search collapses to compact input

## ♾️ Infinite Scroll
- Trigger: `scrollY + innerHeight >= docHeight - 500`
- Load indicator: spinning Tailwind loader + text
- Fallback: "Load More" button if JS disabled
- Livewire: `#[Url] public $page = 1;`

## ⚡ Livewire 4 Loading States

### Automatic Loading Attributes
```blade
<!-- Button with loading states -->
<button 
    wire:click="loadMore"
    wire:loading.attr="disabled"
    wire:loading.class="opacity-50"
    class="px-6 py-3 bg-stage-600 text-white rounded-xl transition-all"
>
    <span wire:loading.remove>Load More</span>
    <span wire:loading>
        <svg class="animate-spin h-5 w-5 inline mr-2" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Loading...
    </span>
</button>

<!-- Loading spinner on form submit -->
<form wire:submit="save">
    <button type="submit" wire:loading.attr="disabled">
        <span wire:loading.remove>Save Changes</span>
        <span wire:loading>Saving...</span>
    </button>
</form>
```

### Skeleton Loading for Grid
```blade
<div wire:init="loadAlbums">
    <!-- Skeleton loader -->
    <div wire:loading>
        <div class="masonry-grid">
            @foreach(range(1, 12) as $skeleton)
                <div class="animate-pulse bg-gray-200 dark:bg-gray-700 rounded-2xl h-96"></div>
            @endforeach
        </div>
    </div>
    
    <!-- Actual content -->
    <div wire:loading.remove>
        <div class="masonry-grid">
            @foreach($albums as $album)
                @livewire('frontend.ui.album-card', ['album' => $album], key($album['id']))
            @endforeach
        </div>
    </div>
</div>
```

### Target Specific Elements with `wire:target`
```blade
<!-- Show loader only when search is updating -->
<div wire:loading wire:target="search">
    <span class="text-sm text-gray-500">Searching...</span>
</div>

<input type="text" wire:model.live.debounce.300ms="search">

<!-- Multiple targets -->
<div wire:loading wire:target="save, delete">
    Processing...
</div>

<button wire:click="save">Save</button>
<button wire:click="delete">Delete</button>
```

### Polling for Real-time Updates
```blade
<!-- Poll every 30 seconds -->
<div wire:poll.30s="checkNewAlbums">
    @if($newAlbumsCount > 0)
        <div class="bg-stage-500 text-white p-2 rounded">
            {{ $newAlbumsCount }} new albums available!
        </div>
    @endif
</div>

<!-- Poll only when visible -->
<div wire:poll.visible.60s="refreshData">
    Latest updates: {{ $lastUpdate }}
</div>

<!-- Keep-alive polling (keeps session alive) -->
<div wire:poll.keep-alive.120s="keepAlive"></div>
```

### Loading States with Alpine.js
```blade
<div x-data="{ loading: false }" 
     x-on:livewire:updating.window="loading = true"
     x-on:livewire:updated.window="loading = false">
    
    <div x-show="loading" x-cloak class="fixed top-4 right-4 z-50">
        <div class="bg-stage-600 text-white px-4 py-2 rounded shadow-lg">
            Loading...
        </div>
    </div>
    
    <!-- Livewire component content -->
</div>
```

### Disabled State During Request
```blade
<form wire:submit="save">
    <button 
        type="submit"
        wire:loading.attr="disabled"
        class="bg-stage-600 text-white px-4 py-2 rounded disabled:opacity-50"
    >
        Submit
    </button>
</form>
```

### Progress Indicator
```blade
<div wire:loading>
    <div class="fixed top-0 left-0 right-0 h-1 bg-stage-500 z-50">
        <div class="h-full bg-stage-600 animate-pulse"></div>
    </div>
</div>
```

## 🌍 Language Switcher
- Position: Header, left of dark mode toggle
- Default: `ru`
- Options: `ru`, `en`, `eo`
- Storage: `localStorage.setItem('language', lang)`
- HTML attr: `document.documentElement.lang = lang`

## ♿ Accessibility
- `aria-label` on all interactive elements
- Focus states: `focus:ring-2 focus:ring-stage-500`
- Color contrast: ≥ 4.5:1 (WCAG AA)
- Skip link: hidden but focusable
- `prefers-reduced-motion`: disable `animate-pulse-slow`

## ⚡ Performance UX
- Lazy images: `loading="lazy"`, `decoding="async"`
- Hover effects: CSS `transform` only (GPU accelerated)
- No layout shift: reserve image aspect ratios
- Debounce heavy inputs
