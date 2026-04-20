# 📚 StagePhoto.ru AI Context Bundle
> Master index for AI training, context injection, and overflow handling.

# 🎯 Project Vision & Core Philosophy

## 📌 Core Principles
1. **Albums-First UX**: Homepage (`/`) = browsable album grid. Zero marketing fluff.
2. **Community-Driven in General**: Fans admire their idols viewing photographs.
3. **Community-Driven for Creators**: Photographers submit work (their photos). Bands/theaters request photographers.
4. **Photographer-Centric**: Every photographer gets a dedicated portfolio page.
5. **Performance-First**: Lazy loading, CSS-only animations, minimal JS, CDN-ready images.
6. **No Promo Homepage**: All "about us", policies, FAQs live in footer/static pages.

## 🎯 Target Audience
- Fans of all sorts of live performances from classic theater through experimental stage to rock/metal concerts and festivals
- Concert & theater photographers (contributors)
- Rock/metal bands, festival organizers, theater managers (requestors)
- Photography enthusiasts (consumers)

## 🚫 Anti-Patterns
- ❌ Max-width containers (`container mx-auto`) on main content
- ❌ Hardcoded light/dark mode without OS detection
- ❌ JavaScript-heavy filtering (use Livewire + Alpine only)
- ❌ Ignoring mobile-first or zoom-level behavior
- ❌ Using spatie packages in the project

## ✅ Success Metrics
- Instant visual engagement on load
- Seamless infinite scroll without layout shift
- Photographer booking requests > 30% conversion
- Sub-2s LCP on 3G

# 🛠️ Technical Stack & Architecture

## 📦 Core Framework
- **PHP**: 8.4+
- **Laravel**: 13.x
- **Livewire**: 4.x
- **Tailwind**: 4.x
- **Frontend**: Livewire 4 + Alpine.js 3
- **Styling**: Tailwind CSS 4
- **DB**: MySQL 8+ or PostgreSQL 15+
- **Cache/Queue**: Redis

## 🧩 Key Patterns
- **Livewire 4**: Use `#[Url]`, `wire:model.live.debounce.300ms`, islands for pagination
- **Alpine.js**: `$store` for global state, `x-data` for local interactions
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

# 🎨 Design System & Layout Rules

## 🌐 Full-Width Mandate
- **NEVER** use `container`, `max-w-*`, or centered constraints on main content
- Header, filter bar, grid, footer: all `width: 100vw` with responsive padding
- Grid: `display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));`
- Gaps scale with viewport: `1rem` (mobile) → `2rem` (≥1920px)

## 🎨 Color Palette
```css
@theme {
  --color-stage-50: #fff8f0;
  --color-stage-100: #ffedd5;
  --color-stage-500: #f97316;
  --color-stage-600: #ea580c;
  --color-stage-900: #1e1b4b;
  --color-spotlight: #fef08a;
}
```
- **Dark Mode**: `@variant dark { ... }` or `dark:` prefixes
- **Auto-Detect**: `window.matchMedia('(prefers-color-scheme: dark)')` → localStorage fallback

## 🔤 Typography
- Font: `Inter`, system-ui fallback
- Headings: `font-bold`, `tracking-tight`
- Body: `text-sm` (mobile) → `text-base` (desktop)
- Line height: `1.5` for readability

## 📱 Responsive Breakpoints
| Size | Columns | Gap | Padding |
|------|---------|-----|---------|
| `<640px` | 2 | 1rem | `px-4` |
| `≥640px` | 2 | 1.25rem | `px-6` |
| `≥1024px` | 3-4 | 1.5rem | `px-8` |
| `≥1440px` | 4-5 | 1.75rem | `px-10` |
| `≥1920px` | 5-6 | 2rem | `px-12` |

## 🌓 Dark Mode Rules
1. Check OS preference on load
2. Respect `localStorage('darkMode')`
3. Listen to system changes in real-time
4. Toggle button always visible next to language switcher

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

# 🧩 Component Specifications

## `<x-album-card>`
- **Props**: `$album`, `$photographer`, `$showRequestBtn = true`
- **Structure**: Cover → hover overlay → title → meta → actions
- **States**: `✨ NEW`, `🔥 FEATURED`, `👤 YOUR WORK` badges
- **Hover**: Scale cover 1.05x, fade overlay, show photographer + date
- **Actions**: `View Album` (primary), `Request` (secondary, auth-gated)

## Filter Bar (`<x-filter-bar>`)
- **Layout**: Flex wrap, pills + dropdown + search
- **State**: Synced with Livewire `AlbumFilters` component
- **Responsive**: Pills scroll, search collapses on mobile

## Header (`<x-header>`)
- **Logo**: `StagePhoto.ru` with gradient icon
- **Right**: Search (desktop) → Lang Switcher → Dark Toggle → Auth Buttons
- **Sticky**: `top-0 z-50 backdrop-blur-sm`

## Language Switcher (`<x-lang-switcher>`)
- **Structure**: 3 buttons in pill container
- **Active**: `bg-stage-600 text-white`
- **Inactive**: `bg-gray-100 dark:bg-gray-800 hover:bg-white/50`
- **Alpine**: `x-data="{ lang: localStorage.getItem('language') || 'ru' }"`

## Request Modal (`<x-request-modal>`)
- **Trigger**: `@click="$dispatch('open-modal', { id: 'request', photographerId })`
- **Fields**: Message, date range, venue, budget (optional)
- **Validation**: Alpine + Livewire `wire:submit`
- **Feedback**: Success toast, email notification

# 🗺️ Livewire SPC structure

```
resources/views/
├── layouts/
│   └── app.blade.php                ← / (front-end layout)
└── components/frontend/             ← / (top-level front-end livewire components folder)
    ├── islands/                     ← / livewire 4 islends
    │   └── album-grid.blade.php     ← album-grid livewire 4 island
    ├── pages/                       ← / livewire 4 web site pages
    │   └── home.blade.php           ← home page livewire 4 component
    │   └── album-grid.blade.php     ← album-grid livewire 4 component
    └── ui/                          ← / livewire 4 user interfaces
   [qwen-web.md](qwen-web.md)     ├── album-card.blade.php     ← album-card livewire 4 components
        ├── footer.blade.php         ← footer livewire 4 components
        └── header.blade.php         ← header livewire 4 components
```

## 🔗 URL Rules
- Slugs: lowercase, hyphenated, unique
- Photographer: `@username` or `/photographer/username`
- Static pages: `/about`, `/faq`, `/policy` (no nav prominence)

## 📦 Livewire Islands
- `AlbumFilters` → syncs URL params
- `AlbumsGrid` → handles pagination, infinite scroll
- `RequestForm` → booking flow, validation, notifications
- `DarkModeToggle` → syncs with system + localStorage

# 🤖 AI Workflow & Context Management

## 📥 Context Injection Template
```
You are building StagePhoto.ru. Follow these rules:
1. Read: docs/ai-context/01_PROJECT_VISION.md
2. Read: docs/ai-context/03_DESIGN_SYSTEM.md
3. Read: docs/ai-context/06_ROUTING_FOLIO.md
Task: [Describe exact component/page]
Output: [Specify file path, framework, constraints]
```

## 🔄 Overflow Strategy (Qwen / Cursor)
1. When context nears limit: summarize current state
2. Detach non-essential files, keep only `00_INDEX.md` + task-specific `XX_*.md`
3. Use `@docs/ai-context/05_COMPONENT_SPEC.md` for UI tasks
4. Use `@docs/ai-context/07_AI_WORKFLOW.md` to reset state

## 🛠️ VS Code / Cursor Setup
- Enable `editor.suggest.showWords: true`
- Use `.cursorrules` for persistent AI behavior
- Add `docs/ai-context/` to `.gitignore` if sensitive (optional)
- Use `// @ai-context:docs/ai-context/03_DESIGN_SYSTEM.md` in code comments

## 🚨 AI Anti-Patterns
- ❌ Ignoring full-width rule
- ❌ Adding promo to `/`
- ❌ Using `container` or `max-w-*` on main sections
- ❌ Hardcoding light/dark without system detection
- ❌ Skipping RU default language

## ✅ Prompt Templates
```
Generate: [Component] using Livewire 4 + Alpine + Tailwind 4
Constraints: Full-width, responsive grid, dark mode auto-detect, RU default
Output: Blade file + component class + Alpine state
```
```
Fix: [Bug/Issue] in [File]
Rules: Maintain full-bleed layout, preserve language switcher position, keep dark mode sync
```
```
Optimize: [Page/Component] for performance
Target: LCP < 2s, CLS 0, JS < 50kb, lazy images, CSS grid only
```
