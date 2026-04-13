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
