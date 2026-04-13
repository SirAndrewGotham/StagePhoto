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
