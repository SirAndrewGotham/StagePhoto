# 🤖 AI Workflow & Context Management

## 📥 Context Injection Template

```
You are building StagePhoto.ru. Follow these rules:
1. Read: docs/01_PROJECT_VISION.md
2. Read: docs/03_DESIGN_SYSTEM.md
3. Read: docs/06_ROUTING.md
Task: [Describe exact component/page]
Output: [Specify file path, framework, constraints]
```

## 🔄 Overflow Strategy (Qwen / Cursor / Claude)

1. When context nears limit: summarize current state
2. Detach non-essential files, keep only `00_INDEX.md` + task-specific `XX_*.md`
3. Use `@docs/05_COMPONENT_SPEC.md` for UI tasks
4. Use `@docs/07_AI_WORKFLOW.md` to reset state

## 🛠️ VS Code / Cursor Setup

### Workspace Settings (`.vscode/settings.json`)
```json
{
  "editor.suggest.showWords": true,
  "files.associations": {
    "⚡*.blade.php": "blade",
    "*.blade.php": "blade"
  },
  "livewire.componentsPath": "resources/views/components/frontend",
  "livewire.useInlineScripts": true
}
```

### Cursor Rules (`.cursor/rules/livewire-4.mdc`)
```yaml
---
description: Livewire 4 specific coding standards for StagePhoto.ru
globs: resources/views/components/frontend/**/*.blade.php
---

# Livewire 4 Rules

## Component Creation
- Always use SFC format with ⚡ prefix
- File path: `resources/views/components/frontend/⚗️component-name.blade.php`
- Use anonymous class: `new class extends Component { }`

## Properties
- Use `#[Url]` for URL-synced properties
- Use `#[Computed]` for expensive operations
- Use `#[On]` for event listeners

## Templates
- Use `wire:model.live.debounce.300ms` for search inputs
- Use `wire:key` in loops
- Use `wire:loading` attributes for loading states
```

## 🚨 AI Anti-Patterns (StagePhoto.ru Specific)

- ❌ Ignoring full-width rule (no `container` or `max-w-*` on main sections)
- ❌ Adding promo/homepage marketing content to `/` (albums grid only)
- ❌ Hardcoding light/dark without system detection
- ❌ Skipping RU default language
- ❌ Creating components outside `resources/views/components/frontend/`
- ❌ Using `@livewire('component')` without proper namespace
- ❌ Forgetting `⚡` prefix for SFC files
- ❌ Using Spatie packages (project policy)
- ❌ **Putting buttons on album cards** (entire card should be clickable, Request button in sidebar)
- ❌ **Storing original images without generating WebP variants**
- ❌ **Skipping watermark on display images**
- ❌ **Using JPEG/PNG for web delivery** (use WebP instead)

## ✅ Implemented Features (Current State)

### Core Features
- [x] Album grid with infinite scroll
- [x] Full-width responsive masonry layout
- [x] Dark mode with system preference detection
- [x] Multi-language support (RU, EN, EO) via Laravel localization
- [x] Album show page with photo grid
- [x] Photo modal with lightbox navigation
- [x] Comment system (albums and photos)
- [x] Rating system (5-star)
- [x] Like system for comments
- [x] Tag system for albums
- [x] Category system with translations
- [x] Request system for photographers

### Image Processing (To Be Implemented)
- [ ] WebP conversion on upload
- [ ] Multiple size variants generation
- [ ] Watermark application
- [ ] Album cover variants (square + hero)

### Database Tables Created
| Table | Purpose |
|-------|---------|
| `albums` | Album metadata |
| `photos` | Photo metadata and paths |
| `categories` | Music/theater categories |
| `category_translations` | Multi-language category names |
| `tags` | Album tags |
| `taggables` | Polymorphic tag relationships |
| `comments` | Threaded comments with likes |
| `ratings` | 5-star ratings (polymorphic) |
| `likes` | Comment likes (polymorphic) |
| `requests` | Photographer request forms |

## ✅ Prompt Templates

### Generate Component
```
Generate a Livewire 4 SFC component for [Component Name]
Location: resources/views/components/frontend/⚡component-name.blade.php
Features:
- Full-width responsive grid
- Dark mode auto-detect with Alpine.js store
- Russian default language
- Uses #[Url] for filter persistence
- Includes loading skeleton states
Output: Complete SFC file with PHP class and Blade template
```

### Fix Bug
```
Fix: [Bug/Issue] in resources/views/components/frontend/⚡component-name.blade.php
Rules:
- Maintain full-bleed layout (no container classes)
- Preserve language switcher position in header
- Keep dark mode sync with Alpine store
- Ensure Livewire 4 #[Computed] syntax
- Add proper error handling
```

### Optimize Performance
```
Optimize: resources/views/components/frontend/⚡album-grid.blade.php
Targets:
- LCP < 2s
- CLS < 0.1
- JS < 50KB
- Lazy load images with loading="lazy"
- Implement wire:key for list stability
- Add skeleton loading states
- Use CSS grid only (no JS layout)
```

## 📂 File Reference Quick Guide

| Purpose | File Path | Key Patterns |
|---------|-----------|--------------|
| Header | `components/frontend/⚡header.blade.php` | Alpine store access, language switcher |
| Filter Bar | `components/frontend/⚡filter-bar.blade.php` | `#[Url]`, `wire:model.live` |
| Album Grid | `components/frontend/⚡album-grid.blade.php` | `#[Computed]`, pagination, skeleton |
| Album Card | `components/frontend/⚡album-card.blade.php` | Props, event dispatching |
| Footer | `components/frontend/⚡footer.blade.php` | Static links, translation keys |
| Home Page | `livewire/pages/⚡home.blade.php` | Layout extends, component assembly |

## 🔍 Common Livewire 4 Mistakes to Avoid

### ❌ Wrong
```php
public $search;
public function updatedSearch() { }
```

### ✅ Correct
```php
#[Url]
public $search = '';
public function updatedSearch($value) { }
```

### ❌ Wrong
```php
public function getAlbumsProperty() { }
```

### ✅ Correct
```php
#[Computed]
public function albums() { }
```

### ❌ Wrong
```php
$this->emit('event', $data);
```

### ✅ Correct
```php
$this->dispatch('event', data: $data);
```

### ❌ Wrong
```php
@livewire('album-card')
```

### ✅ Correct
```php
@livewire('frontend.album-card', ['album' => $album], key($album['id']))
```

## 🧪 AI Testing Checklist

Before submitting code, AI should verify:

- [ ] Component uses `⚡` prefix in filename
- [ ] Component located in correct directory
- [ ] Uses `new class extends Component { }` syntax
- [ ] Imports proper attributes (`use Livewire\Attributes\Url;`)
- [ ] No `container` or `max-w-*` classes on main content
- [ ] Dark mode uses Alpine store (`$store.theme.dark`)
- [ ] Translations use Alpine store (`$store.i18n.t()`)
- [ ] Loading states use `wire:loading` attributes
- [ ] Lists use `wire:key` with unique identifiers
- [ ] Images use `loading="lazy"`

## 📝 Commit Message Format for AI-Generated Code

```
type(scope): description

Type: feat|fix|docs|style|refactor|perf|test
Scope: component-name|config|docs|routes

Example:
feat(album-grid): add infinite scroll with skeleton loading

- Implement wire:poll for real-time updates
- Add #[Computed] for filtered albums
- Add loading skeleton states
- Fix dark mode compatibility
```

## 🔄 Resetting AI Context

When context becomes too large, use this reset template:

```
CONTEXT RESET - StagePhoto.ru

Active files being worked on:
- docs/00_INDEX.md
- docs/06_ROUTING.md

Ignore all previous context. Current task: [describe task]

Key constraints:
1. Livewire 4 SFC in resources/views/components/frontend/
2. No max-width containers
3. Dark mode via Alpine store
4. Russian default language
5. No Spatie packages

Proceed with fresh context.
```
