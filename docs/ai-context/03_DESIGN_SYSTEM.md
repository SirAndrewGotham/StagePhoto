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
