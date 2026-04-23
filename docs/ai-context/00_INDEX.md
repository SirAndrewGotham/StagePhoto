# 📚 StagePhoto.ru AI Context Bundle

> Master index for AI training, context injection, and overflow handling.

## 🎯 Project Overview

**StagePhoto.ru** is a concert and theater photography platform built with:
- **Laravel 13** + **Livewire 4** (SPC - Single Page Components with ⚡ prefix)
- **Tailwind CSS 4** + **Alpine.js 3**
- Components located in `resources/views/components/frontend/`

## 📂 Context Files Index
| File                     | Purpose | When to Read |
|--------------------------|---------|--------------|
| `01_PROJECT_VISION.md`   | Core principles, target audience, anti-patterns | Always - defines project soul |
| `02_TECH_STACK.md`       | Livewire 4 syntax, configuration, soft deletes, image processing | Before writing any code |
| `03_DESIGN_SYSTEM.md`    | Full-width mandate, colors, dark mode, responsive breakpoints | Before any UI work |
| `04_UX_RULES.md`         | Filter bar, infinite scroll, loading states, accessibility | Before interaction design |
| `05_COMPONENT_SPEC.md`   | Album card, header, footer, request modal, trash manager, photo uploader | Before component creation |
| `06_ROUTING.md`          | File structure, route registration, naming conventions, protected routes | Before creating new pages |
| `07_AI_WORKFLOW.md`      | Prompt templates, anti-patterns, testing checklist, implemented features | For optimal AI collaboration |
| `08_IMAGE_PROCESSING.md` | Image dimensions, formats, watermarks, processing workflow | When implementing uploads |
| `09_STATUS_SYSTEM.md`    | Approval workflow, status values, moderation, admin actions | When working with content moderation |
| `10_ENTITIES.md`         | Theaters, bands, individuals, entity profiles, contact privacy, relationships | When working with personas/entities |

## 🚀 Quick Start for New AI Session

**Read these 5 files first:**
1. `01_PROJECT_VISION.md` - Understand what we're building
2. `03_DESIGN_SYSTEM.md` - Understand layout constraints (no containers!)
3. `06_ROUTING.md` - Understand where components live
4. `08_IMAGE_PROCESSING.md` - Understand image requirements
5. `09_STATUS_SYSTEM.md` - Understand approval workflow

## Current Implementation Status (Updated)

| Feature | Status |
|---------|--------|
| Album grid with infinite scroll | ✅ Complete |
| Filter bar (genre, type, sort) | ✅ Complete |
| Album show page | ✅ Complete |
| Photo modal with comments | ✅ Complete |
| Rating system | ✅ Complete |
| Like system for comments | ✅ Complete |
| Request system | ✅ Complete |
| Multi-language (RU, EN, EO) | ✅ Complete |
| Dark mode | ✅ Complete |
| WebP conversion | ✅ Complete |
| Watermark application | ✅ Complete |
| Album cover variants | ✅ Complete |
| ZIP batch upload | ✅ Complete |
| Drag-and-drop upload | ✅ Complete |
| Duplicate detection | ✅ Complete |
| Soft deletes | ✅ Complete |
| Trash manager | ✅ Complete |
| Unsorted albums | ✅ Complete |
| Move photos between albums | ✅ Complete |
| Photographer upload interface | ✅ Complete |
| Status & approval system | ✅ Complete |
| Entity system (theaters/bands/individuals) | ✅ Complete |
| Entity profile pages | ✅ Complete |
| Admin dashboard | ⏳ Pending |

## ⚡ Critical Rules (Never Break)

- ❌ NO `container` or `max-w-*` on main content
- ❌ NO Spatie packages
- ✅ Components in `resources/views/components/frontend/⚗️*.blade.php`
- ✅ Dark mode via Alpine store (`$store.theme.dark`)
- ✅ Russian default language (`$store.i18n.locale = 'ru'`)

## 🔗 Repository

**GitHub:** `github.com/SirAndrewGotham/StagePhoto`

## 📝 How to Use This Bundle

**For DeepSeek:**
```
I'm working on StagePhoto.ru. Please read docs/00_INDEX.md first, then docs/01_PROJECT_VISION.md, docs/03_DESIGN_SYSTEM.md, and docs/06_ROUTING.md. Then help me with [specific task].
```

**For Cursor/Claude:**
```
@docs/00_INDEX.md @docs/01_PROJECT_VISION.md @docs/03_DESIGN_SYSTEM.md
Task: [description]
```

**For GitHub Copilot:**
Include relevant docs in your workspace or add as context in Copilot Chat.
