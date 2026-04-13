# 🗺️ Folio Routing Structure

```
resources/views/pages/
├── index.blade.php                 ← / (albums grid only)
├── album/
│   ├── {album}.blade.php           ← /album/{slug}
│   └── {album}/{photo}.blade.php   ← /album/{slug}/{photo-id} (lightbox)
├── photographer/
│   ├── {user}.blade.php            ← /photographer/{username}
│   └── {user}/request.blade.php    ← /photographer/{username}/request
├── bands/
│   └── request-photographer.blade.php ← /bands/request
├── submit/
│   ├── album.blade.php             ← /submit/album
│   └── photo.blade.php             ← /submit/photo
└── ... (static: about, faq, policy, privacy live in footer, NOT nav)
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
