# 🛠️ Technical Stack & Architecture

## 📦 Core Framework
- **PHP**: 8.4+
- **Laravel**: 13.x
- **Livewire**: 4.x
- **Tailwind**: 4.x
- **Routing**: `laravel-folio` (file-based)
- **Frontend**: Livewire 4 + Alpine.js 3
- **Styling**: Tailwind CSS 4
- **DB**: MySQL 8+ or PostgreSQL 15+
- **Cache/Queue**: Redis

## 🧩 Key Patterns
- **Livewire 4**: Use `#[Url]`, `wire:model.live.debounce.300ms`, islands for pagination
- **Alpine.js**: `$store` for global state, `x-data` for local interactions
- **Folio**: `resources/views/pages/` → direct URL mapping. No route files needed.
- **Tailwind 4**: `@variant dark`, `@theme` in CSS, `prefers-color-scheme` native support
- **Images**: Intervention Image v3 → WebP/AVIF, `loading="lazy"`, `srcset`

## 🚫 Banned
- jQuery, Vue, React, Inertia, Bootstrap, custom CSS files (unless `@layer`)
- `container`, `max-w-*` on main layout sections
- Client-side routing for album browsing (use SSR + Livewire)

## ✅ Performance Rules
- Debounce all filters (`300ms`)
- Paginate albums via Livewire (12/page)
- Use CSS `grid-template-columns: repeat(auto-fill, minmax(280px, 1fr))`
- Preload critical fonts, defer Alpine/Livewire scripts
