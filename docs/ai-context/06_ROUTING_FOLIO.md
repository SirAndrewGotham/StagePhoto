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
        ├── album-card.blade.php     ← album-card livewire 4 components
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
