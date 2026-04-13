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
